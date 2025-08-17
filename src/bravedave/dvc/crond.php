<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 * 
 * MIT License
 *
*/

namespace bravedave\dvc;

use Fiber;
use config as rootConfig;

final class crond {

  public static $semaphore = null;
  public static $exitSemaphore = null;
  protected static $exit = false;

  protected static function cronExit(): bool {

    if (self::$exit) return true; // already set to exit

    // Check for exit semaphore
    if (file_exists(self::$exitSemaphore)) {

      self::$exit = true; // set exit flag
      if (@unlink(self::$exitSemaphore)) {
        logger::info('<cron: exit semaphore found and removed, exiting>');
      } else {
        logger::info('<cron: exit semaphore found but could not be removed, exiting>');
      }
    }

    return self::$exit; // return current exit status
  }

  public static function cron() {
    $debug = false;
    // $debug = true;

    $crons = rootConfig::crons();
    // if there are no crons defined, exit early
    if (empty($crons)) {
      if ($debug) logger::debug('<cron: no crons defined, exiting>');
      return;
    }

    if (!self::$semaphore) self::$semaphore = rootConfig::dataPath() . '/cron.semaphore';
    if (!self::$exitSemaphore) self::$exitSemaphore = rootConfig::dataPath() . '/cron.exit';

    // Check if semaphore exists
    if (file_exists(self::$semaphore)) {

      $mtime = filemtime(self::$semaphore);
      if ($mtime && (time() - $mtime < 600)) { // 10 minutes

        if ($debug) logger::debug('<cron: semaphore exists and is recent, exiting>');
        return;
      } else {

        // Old semaphore, remove
        unlink(self::$semaphore);
        logger::info('<cron: old semaphore removed>');
      }
    }

    // Create semaphore
    touch(self::$semaphore);

    $fibres = [];
    foreach ($crons as $source) {

      $mod = new $source();
      if (is_callable($mod)) {

        $fibre = $mod();
        if ($fibre instanceof Fiber) $fibres[] = $fibre;
      }
    }

    array_walk($fibres, fn($f)  => $f->start());

    $maxRuns = config::$CROND_MAX_RUNS ?? 3;
    if ($debug) logger::debug(sprintf('<cron cycling for %s> %s', $maxRuns, logger::caller()));

    $i = 0;
    while (($maxRuns === 0 || $i < $maxRuns) && !self::cronExit()) {

      if ($debug) logger::debug(sprintf('<cron cycle %s> %s', $i, logger::caller()));
      foreach ($fibres as $fibre) {

        if ($fibre->isSuspended()) {

          try {
            $fibre->resume(false);
            if (self::cronExit()) break;
          } catch (\Throwable $e) {
            logger::info('<cron: fibre error - ' . $e->getMessage() . '>');
          }
        }
      }

      // Touch semaphore to update timestamp
      touch(self::$semaphore);
      sleep(1);
      $i++;
    }

    // Graceful exit for fibres
    foreach ($fibres as $fibre) {
      if ($fibre->isSuspended()) {
        try {
          $fibre->resume(true); // true to indicate graceful exit
        } catch (\Throwable $e) {
          logger::info('<cron: fibre exit - ' . $e->getMessage() . '>');
        }
      }
    }

    // Remove semaphore on exit
    if (file_exists(self::$semaphore)) {
      unlink(self::$semaphore);
      if ($debug) logger::debug('<cron: semaphore removed on exit>');
    }

    if ($debug) logger::debug(sprintf('<cron finished> %s', logger::caller()));
  }
}
