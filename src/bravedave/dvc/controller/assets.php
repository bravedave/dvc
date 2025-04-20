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
  cssmin,
  jslib,
  logger,
  resources,
  Response,
  ServerRequest
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
    // logger::info( sprintf('<%s/%s> %s', $type, $version, __METHOD__));

    if ('fonts' == $type) {

      Response::serveBootStrap($type, $version);
    } elseif (4 == (int)$version) {

      Response::serveBootStrap($type);
    } elseif (5 == (int)$version) {

      $request = new ServerRequest;
      if ('css' == $type) {

        Response::serveBootStrap5($type, $request->getQueryParam('t') ?: null);
      } elseif (in_array($type, ['polyfill', 'js'])) {

        Response::serveBootStrap5($type);
      }
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

      // $files = [];
      // foreach (jslib::$brayworthlibFiles as $f) {
      //   $path = dirname(__DIR__) . DIRECTORY_SEPARATOR . $f;
      //   // printf( '%s<br />', $path);
      //   if ($_f = realpath($path)) {
      //     $key = basename($_f);
      //     $files[$key] = $_f;
      //   }
      // }
      $files = jslib::getLibFiles();
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

  public function esse() {

    // logger::info( sprintf('<%s> %s', sprintf('%s/esse/esse.css', dirname(__DIR__)), __METHOD__));
    // logger::info( sprintf('<%s> %s', application::app()->getInstallPath(), __METHOD__));

    // cssmin::viewcss([
    //   'debug' => false,
    //   'libName' => 'home/css/default',
    //   'cssFiles' => [
    //     sprintf('%s/esse/esse.css', dirname(__DIR__)),
    //     sprintf('%s/css/brayworth.context.css', dirname(__DIR__)),
    //     sprintf('%s/css/brayworth.autoResize.css', dirname(__DIR__)),
    //     sprintf('%s/css/brayworth.markdown.css', dirname(__DIR__)),
    //     sprintf('%s/esse/esse.menu.css', dirname(__DIR__)),
    //     sprintf('%s/esse/esse.markdown.css', dirname(__DIR__)),
    //   ],
    //   'libFile' => config::tempdir()  . 'bravedave_dvc_esse.css'
    // ]);

    cssmin::viewcss([
      'debug' => false,
      'libName' => 'home/css/default',
      'cssFiles' => cssmin::esseFiles(),
      'libFile' => config::tempdir()  . 'bravedave_dvc_esse.css'
    ]);

    // logger::info(sprintf('<%s> %s', config::tempdir()  . 'bravedave_dvc_esse.css', __METHOD__));
  }

  public function fullcalendar($type = 'css') {
    sys::serveFullcalendar($type);
  }

  public function jquery($version = 3) {
    if ($version >= 4) {

      // Response::serve(dirname(__DIR__) . '/js/jquery-4.0.0-beta.min.js');
      Response::serve(resources::jquery4());
    } elseif ('3.4' == config::$JQUERY_VERSION) {

      Response::serve(dirname(__DIR__) . '/js/jquery-3.4.1.min.js');
    } else {

      Response::serve(dirname(__DIR__) . '/js/jquery-3.7.1.min.js');
    }
  }

  public function module($file) {
    // logger::info( sprintf('<%s> %s', $file, __METHOD__));

    if ($file = preg_replace( '/[^a-zA-Z0-9\_\-]/', '', $file)) {

      $path = resources::module($file);
      Response::serve($path);
    }
  }

  public function pdflib() {

    Response::serve(dirname(__DIR__) . '/js/pdf-lib.min.js');
  }

  public function quill($type = 'css') {
    // logger::info( sprintf('<%s/%s> %s', $type, $version, __METHOD__));

    Response::serveQuill($type);
  }

  public function toastui($type = 'css') {
    // logger::info( sprintf('<%s/%s> %s', $type, $version, __METHOD__));

    Response::serveToastUI($type);
  }

  public function tinymce($path = '') {

    parent::js('tinymce');
  }
}
