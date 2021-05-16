<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc\core;

abstract class bs {
  static $VERSION = 4;

  static function data( string $tag, string $value = null) : string {
    if ( $value) {
      return sprintf(
        '%s-%s="%s"',
        5 == self::$VERSION ? 'data-bs' : 'data',
        $tag,
        $value

      );

    }
    else {
      return sprintf(
        '%s-%s',
        5 == self::$VERSION ? 'data-bs' : 'data',
        $tag

      );

    }

  }

}
