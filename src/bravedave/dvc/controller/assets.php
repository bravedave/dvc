<?php
/*
 * Copyright (c) 2026 David Bray
 * Licensed under the MIT License. See LICENSE file for details.
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

class assets extends Controller {
  protected $RequireValidation = false;

  protected function _index() {
  }

  protected function before() {
    self::application()::app()->exclude_from_sitemap = true;
    parent::before();
  }

  public function bootstrap($type = 'css', $version = 4) {

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

      $files = jslib::getLibFiles();
      if ($type == 'bundle') array_unshift($files, resources::jquery4());

      jslib::viewjs([
        'debug' => false,
        'libName' => 'brayworth',
        'jsFiles' => $files,
        'libFile' => config::tempdir()  . '_brayworth_has_dayjs.4.js'
      ]);
    }
  }

  public function esse() {

    cssmin::viewcss([
      'debug' => false,
      'libName' => 'home/css/default',
      'cssFiles' => cssmin::esseFiles(),
      'libFile' => config::tempdir()  . 'bravedave_dvc_esse.css'
    ]);
  }

  public function jquery($version = 3) {
    if ($version >= 4) {

      Response::serve(resources::jquery4());
    } elseif ('3.4' == config::$JQUERY_VERSION) {

      Response::serve(dirname(__DIR__) . '/js/jquery-3.4.1.min.js');
    } else {

      Response::serve(dirname(__DIR__) . '/js/jquery-3.7.1.min.js');
    }
  }

  public function module($file) {

    if ($file = preg_replace('/[^a-zA-Z0-9\_\-]/', '', $file)) {

      $path = resources::module($file);
      Response::serve($path);
    }
  }

  public function pdflib() {

    Response::serve(dirname(__DIR__) . '/js/pdf-lib.min.js');
  }

  public function quill($type = 'css') {

    Response::serveQuill($type);
  }

  public function mermaid($type = 'css') {

    Response::serveMermaid($type);
  }

  public function toastui($type = 'css') {

    Response::serveToastUI($type);
  }

  public function tinymce($path = '') {

    parent::js('tinymce');
  }

  public function tinymce8() {

    $request = new ServerRequest;
    $segments = $request->getSegments();
    $folder = array_shift($segments);

    if ('assets' == $folder) {

      $folder = array_shift($segments);
      if ('tinymce8' == $folder) {

        $root = dirname(__DIR__);
        $path = sprintf('%s/resources/tinymce8/%s', $root, implode('/', $segments));
        if (file_exists($path)) {

          Response::serve($path);
        } else {

          logger::info(sprintf('<tinymce8 / %s not found> %s', $path, __METHOD__));
        }
      } else {

        logger::info(sprintf('<no tinymce8> %s', __METHOD__));
      }
    }
  }
}
