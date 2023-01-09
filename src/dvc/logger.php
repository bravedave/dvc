<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc;

abstract class logger {

  public static function info(array|string $msg): void {

    if (is_array($msg)) {

      array_walk($msg, fn ($m) => error_log($m, 0));
    } else {

      error_log($msg, 0);
    }
  }
}
