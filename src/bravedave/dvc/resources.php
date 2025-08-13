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

  public static function bootstrapIcons(string $type = 'css', string|null $fontFile = null): string {

    if ('fonts' == $type) {

      if (in_array($fontFile, [
        'bootstrap-icons.woff',
        'bootstrap-icons.woff2'
      ])) {

        return __DIR__ . '/css/bootstrap-icons/fonts/' . $fontFile;
      }
      return '';
    }
    return __DIR__ . '/css/bootstrap-icons/bootstrap-icons.css';
  }

  public static function bootstrap5(string $type = 'css'): string {

    if ('js' == $type) {

      return __DIR__ . '/js/bootstrap5/bootstrap.bundle.min.js';
    }

    return __DIR__ . '/css/bootstrap5/bootstrap.min.css';
  }

  public static function jquery4(): string {

    return __DIR__ . '/js/jquery-4.0.0-rc.1.min.js';
    return __DIR__ . '/js/jquery-4.0.0-beta.min.js';
  }

  public static function module(string $file): string {

    $path = __DIR__ . '/js/modules/' . $file . '.js';
    if (file_exists($path)) return $path;

    return '';
  }
}
