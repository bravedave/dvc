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

abstract class resources {

  public static function bootstrap5(string $type = 'css'): string {

    if ('js' == $type) {

      return __DIR__ . '/js/bootstrap5/bootstrap.bundle.min.js';
    }

    return __DIR__ . '/css/bootstrap5/bootstrap.min.css';
  }

  public static function jquery4(): string {

    return __DIR__ . '/js/jquery-4.0.0-beta.min.js';
  }
}
