<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\dvc\controller;

use config, Controller;
use bravedave\dvc\{
  jslib,
  Response
};
use dvc\{
  cssmin
};
use sys;

class assets extends Controller {
  protected $RequireValidation = false;

  protected function _index() {
  }

  protected function before() {
    self::application()::app()->exclude_from_sitemap = true;
    parent::before();
  }

  public function bootstrap($type = 'css', $version = 4) {
    // sys::logger( sprintf('<%s/%s> %s', $type, $version, __METHOD__));

    if ('fonts' == $type) {

      Response::serveBootStrap($type, $version);
    } elseif (4 == (int)$version) {

      Response::serveBootStrap($type);
    } elseif (5 == (int)$version && in_array($type, ['polyfill', 'css', 'js'])) {

      Response::serveBootStrap5($type);
    }
  }

  const dvc_dir = __DIR__ . '/../';

  public function brayworth($type = 'css', $p2 = '') {

    if ('css' == $type) {
      $files = [];

      $lib = 'dvc-4';
      $_files = cssmin::$dvc4Files;

      foreach ($_files as $f) {
        $path = self::dvc_dir . $f;
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
      $a = [
        sprintf('_.timezone = "%s";', config::$TIMEZONE,),

        '_.urlwrite = _.url = ( _url, withProtocol) => {',

        'if ( "undefined" == typeof _url) _url = "";',
        sprintf('if ( !!withProtocol) return `%s%s${_url}`;', \url::$PROTOCOL, \url::$URL),
        sprintf('return `%s${_url}`;', \url::$URL),

        '};'
      ];

      Response::javascript_headers();
      printf('( _ => {%s}) (_brayworth_);', implode($a));
    } else {
      // sys::dump( \jslib::$brayworthlibFiles);
      $files = [];
      foreach (jslib::$brayworthlibFiles as $f) {
        $path = dirname(__DIR__) . DIRECTORY_SEPARATOR . $f;
        // printf( '%s<br />', $path);
        if ($_f = realpath($path)) {
          $key = basename($_f);
          $files[$key] = $_f;
        }
      }

      if ($type == 'bundle') array_unshift($files, dirname(__DIR__) . DIRECTORY_SEPARATOR . 'js/jquery-3.6.3.min.js');

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
    if ('3.4' == config::$JQUERY_VERSION) {

      Response::serve(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'js/jquery-3.4.1.min.js');
    } else {

      // Response::serve(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'js/jquery-3.5.1.min.js'));
      // Response::serve(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'js/jquery-3.6.0.min.js'));
      // Response::serve(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'js/jquery-3.6.1.min.js'));
      Response::serve(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'js/jquery-3.6.3.min.js');
    }
  }

  public function tinymce($path = '') {
    // sys::logger(sprintf('<%s> %s', $this->Request->getUri(), __METHOD__));
    if (preg_match('/(\.min\.css|\.css)$/', $uri = $this->Request->getUri())) {
      $file = preg_replace('@^assets/tinymce/@', '', $uri);
      // sys::logger( sprintf('<%s> %s', $file, __METHOD__));

      $_f = sprintf(
        '%s/%s',
        jslib::tiny6_dir(),
        $file
      );

      file_exists($_f) ?
        Response::serve($_f) :
        sys::logger('error serving lib tinymce.css');

      //~ sys::logger( sprintf( 'serving lib tinymce %s', $this->Request->getUri()));

    } else {
      jslib::tiny6serve('tinymce-dvc', 'autolink,lists,advlist,table,image,link');
    }
  }
}
