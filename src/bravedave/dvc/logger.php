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

abstract class logger {

  public static function debug(array|string $msg): void {

    self::info($msg);
  }

  public static function info(array|string $msg): void {

    if (is_array($msg)) {

      array_walk($msg, fn ($m) => error_log($m, 0));
    } else {

      error_log($msg, 0);
    }
  }

  public static function sql(string $msg) {

    self::info(preg_replace(["@\r\n@", "@\n@", "@\t@", "@\s\s*@"], ' ', $msg));
  }
}
