<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

use dvc\cssmin;
use dvc\jslib;

class assets extends Controller {
  public $RequireValidation = false;

  protected function _index() {
  }

  protected function before() {
    application::app()->exclude_from_sitemap = true;
    parent::before();
  }

  public function bootstrap($type = 'css', $version = 4) {
    // \sys::logger( sprintf('<%s/%s> %s', $type, $version, __METHOD__));

    if ('fonts' == $type) {
      sys::serveBootStrap($type, $version);
    } elseif (4 == (int)$version) {
      sys::serveBootStrap($type);
    } elseif (5 == (int)$version && in_array($type, ['polyfill', 'css', 'js'])) {
      sys::serveBootStrap5($type);
    }
  }

  public function brayworth($type = 'css', $p2 = '') {
    if ('css' == $type) {
      $files = [];

      $lib = 'dvc-4';
      $_files = cssmin::$dvc4Files;
      // if ( sys::bootStrap_verion()->major >= 5) {
      //   $lib = 'dvc-5';
      //   $_files[] = 'css/brayworth.bootstrap4.polyfill.css';

      // }

      foreach ($_files as $f) {
        $path = sprintf('%s/../%s', __DIR__, $f);
        if ($_f = realpath($path)) {
          $key = basename($_f);
          $files[$key] = $_f;
        }
      }

      cssmin::viewcss([
        'debug' => false,
        'libName' => $lib,
        'cssFiles' => $files,
        'libFile' => config::tempdir()  . '_' . $lib . '.css'

      ]);
    } elseif ('dopo' == $type) {
      ob_start();

      $files = [];
      foreach (jslib::$brayworthlibDOPOFiles as $f) {
        $path = sprintf('%s/../%s', __DIR__, $f);
        //~ sys::logger( sprintf( '%s', $path));
        if ($_f = realpath($path)) {
          $key = basename($_f);
          $files[$key] = $_f;
        }
      }

      //~ $n = 0;
      foreach ($files as $key => $path) {
        //~ sys::logger( sprintf( "[%s] %s", $key, $path));
        include_once $path;
        print PHP_EOL;
      }

      $out = ob_get_contents();
      ob_end_clean();

      $minifier = new MatthiasMullie\Minify\JS;
      $minifier->add($out);
      $minified =  $minifier->minify();

      Response::javascript_headers();
      print $minified;
    } else {
      // sys::dump( \jslib::$brayworthlibFiles);
      $files = [];
      foreach (jslib::$brayworthlibFiles as $f) {
        $path = sprintf('%s/../%s', __DIR__, $f);
        // printf( '%s<br />', $path);
        if ($_f = realpath($path)) {
          $key = basename($_f);
          $files[$key] = $_f;
        }
      }

      if ($type == 'bundle') {
        array_unshift($files, sprintf('%s/../js/%s', __DIR__, 'jquery-3.3.1.min.js'));
      }

      // sys::dump( $files);

      jslib::viewjs([
        'debug' => false,
        'libName' => 'brayworth',
        'jsFiles' => $files,
        'libFile' => config::tempdir()  . '_brayworth_has_dayjs.3.js'

      ]);
    }
  }

  public function fullcalendar($type = 'css') {
    sys::serveFullcalendar($type);
  }

  public function jquery() {
    if ('3.4' == \config::$JQUERY_VERSION) {
      \sys::serve(sprintf('%s/../js/%s', __DIR__, 'jquery-3.4.1.min.js'));
    } else {
      // \sys::serve(sprintf('%s/../js/%s', __DIR__, 'jquery-3.5.1.min.js'));
      \sys::serve(sprintf('%s/../js/%s', __DIR__, 'jquery-3.6.0.min.js'));
    }
  }
}
