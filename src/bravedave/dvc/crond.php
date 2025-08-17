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

  public static function cron() {
    $debug = false;
    // $debug = true;

    $crons = rootConfig::crons();
    // if there are no crons defined, exit early
    if (empty($crons)) {
      if ($debug) logger::debug('<cron: no crons defined, exiting>');
      return;
    }

    $semaphore = rootConfig::dataPath() . '/cron.semaphore';
    $exitSemaphore = rootConfig::dataPath() . '/cron.exit';

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
    foreach ($crons as $source) {

      $mod = new $source();
      if (is_callable($mod)) {

        $fibre = $mod();
        if ($fibre instanceof Fiber) $fibres[] = $fibre;
      }
    }

    array_walk($fibres, fn($f)  => $f->start());

    $maxRuns = config::$CROND_MAX_RUNS ?? 3;
    $i = 0;
    while ($maxRuns === 0 || $i < $maxRuns) {
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
      $i++;
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
      if ($debug) logger::debug('<cron: semaphore removed on exit>');
    }

    if ($debug) logger::debug(sprintf('<cron finished> %s', logger::caller()));
  }
}
