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

use bravedave, config;
use bravedave\dvc\{cssmin, errsys, hitter};

abstract class sys extends bravedave\dvc\sys {
  protected static $_loglevel = 1;

  static $debug = false;

  public static function logging($level = null) {
    /**
     * Debug logging
     *	I just use 1-5, stuff fromthe application class is output if log level is 3
     **/
    $oldLevel = self::$_loglevel;

    if (!(is_null($level)))
      self::$_loglevel = $level;

    return ($oldLevel);
  }

  public static function logloaderon($b) {
    error_log(sprintf('deprecated : %s', self::traceCaller()));
  }

  public static function loaderCounter(hitter $hitter) {

    error_log(sprintf('deprecated : %s', self::traceCaller()));
  }

  public static function logloader($v) {
    error_log(sprintf('deprecated : %s : %s', $v, self::traceCaller()));
  }

  public static function serveFullcalendar($type = 'css') {
    $root = realpath(__DIR__ . '/public/fullcalendar-4/');
    if ($root) {
      if ('css' == $type) {
        $files = [
          $root . '/core/main.css',
          $root . '/bootstrap/main.css',

        ];

        cssmin::viewcss([
          'debug' => false,
          'libName' => 'fullcalendar4',
          'cssFiles' => $files,
          'libFile' => config::tempdir()  . '_fullcalendar4_tmp.css'
        ]);
      } elseif ('js' == $type) {

        $path = realpath(sprintf('%s/', $root));
        $files = [
          $root . '/core/main.js',
          $root . '/bootstrap/main.js',

        ];

        jslib::viewjs([
          'debug' => false,
          'libName' => 'fullcalendar4',
          'jsFiles' => $files,
          'libFile' => config::tempdir()  . '_fullcalendar4_tmp.js'
        ]);
      }
    } else {
      throw new \Exception('Cannot locate fullcalendar-4 bootstrap');
    }
  }

  public static function set_error_handler() {
    errsys::initiate(false);
  }
}
