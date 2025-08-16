<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * DVC Cron System
 *
 * Usage:
 *   - Place fibre modules in the crond/ folder, each implementing __invoke() and returning a Fiber.
 *   - This script is designed to be called from system cron (e.g., every minute).
 *   - Prevents overlapping runs using a semaphore file (cron.semaphore).
 *   - If the semaphore is older than 10 minutes, it is cleaned up automatically.
 *   - Supports graceful exit via an exit semaphore (cron.exit).
 *   - Each run cycles through all discovered fibres 3 times, resuming each and updating the semaphore.
 *   - Errors in individual fibres are logged, but do not stop other fibres or the main loop.
 *   - On exit, the semaphore is removed and all fibres are given a chance to exit cleanly.
 *
 * Example system cron entry:
 *   * * * * * php /path/to/this/cron.php
 *
*/

require dirname(dirname(__DIR__)) . '/vendor/autoload.php';

use bravedave\dvc\logger;

class cron extends bravedave\dvc\service {

  protected function _cron() {
    $debug = false;
    // $debug = true;

    $semaphore = config::dataPath() . '/cron.semaphore';
    $exitSemaphore = config::dataPath() . '/cron.exit';

    // Check if semaphore exists
    if (file_exists($semaphore)) {

      $mtime = filemtime($semaphore);
      if ($mtime && (time() - $mtime < 600)) { // 10 minutes

        if ($debug) logger::debug('<cron: semaphore exists and is recent, exiting>');
        return;
      } else {

        // Old semaphore, remove
        unlink($semaphore);
        logger::info('<cron: old semaphore removed>');
      }
    }

    // Create semaphore
    touch($semaphore);

    $fibres = [];
    foreach (config::$crons as $source) {

      $mod = new $source();
      if (is_callable($mod)) {

        $fibre = $mod();
        if ($fibre instanceof Fiber) $fibres[] = $fibre;
      }
    }

    array_walk($fibres, fn($f)  => $f->start());

    for ($i = 0; $i < 3; $i++) {
      // Check for exit semaphore
      if (file_exists($exitSemaphore)) {
        if (@unlink($exitSemaphore)) {
          logger::info('<cron: exit semaphore found and removed, exiting>');
        } else {
          logger::info('<cron: exit semaphore found but could not be removed, exiting>');
        }
        break;
      }

      if ($debug) logger::info(sprintf('<cron cycle %s> %s', $i, logger::caller()));
      foreach ($fibres as $fibre) {
        if ($fibre->isSuspended()) {
          try {
            $fibre->resume();
          } catch (\Throwable $e) {
            logger::info('<cron: fibre error - ' . $e->getMessage() . '>');
          }
        }
      }
      // Touch semaphore to update timestamp
      touch($semaphore);
      sleep(1);
    }

    // Graceful exit for fibres
    foreach ($fibres as $fibre) {
      if ($fibre->isSuspended()) {
        try {
          $fibre->resume();
        } catch (\Throwable $e) {
          logger::info('<cron: fibre exit - ' . $e->getMessage() . '>');
        }
      }
    }

    // Remove semaphore on exit
    if (file_exists($semaphore)) {
      unlink($semaphore);
      logger::info('<cron: semaphore removed on exit>');
    }

    logger::info(sprintf('<cron finished> %s', logger::caller()));
  }

  static function crond() {
    $app = new self(application::startDir());
    $app->_cron();
  }
}


if (php_sapi_name() == 'cli' && basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
  cron::crond();
}
