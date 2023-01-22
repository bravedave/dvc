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

use config;

abstract class logger {

  const prefix = 'dvc.INFO';
  const prefix_debug = 'dvc.DEBUG';
  const prefix_deprecated = 'dvc.DEPRECATED';
  const prefix_sql = 'dvc.SQL';

  public static function debug(array|string $msg): void {

    if (config::$LOG_DEBUG) self::info($msg, self::prefix_debug);
  }

  public static function deprecated(array|string $msg): void {

    if (config::$LOG_DEPRECATED) self::info($msg, self::prefix_deprecated);
  }

  public static function info(array|string $msg, string $prefix = self::prefix): void {

    if (is_array($msg)) {

      array_walk($msg, fn ($m) => error_log(sprintf('%s: %s', $prefix,  $m), 0));
    } else {

      error_log(sprintf('%s: %s', $prefix, $msg), 0);
    }
  }

  public static function sql(string $msg): void {

    self::info(preg_replace(["@\r\n@", "@\n@", "@\t@", "@\s\s*@"], ' ', $msg), self::prefix_sql);
  }
}
