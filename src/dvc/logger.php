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

  public static function info(string $msg): void {

    error_log($msg, 0);
  }
}
