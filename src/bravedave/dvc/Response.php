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

use dvc\{userAgent, url};
use config, currentUser, strings;

abstract class Response {

  protected static function _common_headers($modifyTime = 0, $expires = 0) {
    if ($modifyTime) {
      header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $modifyTime) . ' GMT');
      if ($expires) {
        header('Expires: ' . gmdate('D, j M Y H:i:s', time() + $expires) . ' GMT');
        header('Cache-Control: max-age=' . $expires);
      } else {
        header('Expires: ' . gmdate('D, j M Y H:i:s') . ' GMT');      // Date in the past
        header('Cache-Control: no-cache');
        // header('Pragma: no-cache');                          			// HTTP/1.0

      }
    } else {
      header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');  // always modified
      header('Expires: ' . gmdate('D, j M Y H:i:s') . ' GMT');      // Date in the past
      header('Cache-Control: no-cache');
      // header('Pragma: no-cache');                          			// HTTP/1.0

    }
  }

  protected static function _twbs_dir() {

    $dir = realpath(__DIR__ . '/../../../../../twbs');
    if (!$dir) $dir = realpath(__DIR__ . '/../../../vendor/twbs');

    return $dir;
  }

  public static function css_headers($modifyTime = 0, $expires = null) {

    if (is_null($expires)) $expires = config::$CSS_EXPIRE_TIME;

    self::_common_headers($modifyTime, $expires);
    header('Content-type: text/css');
  }

  public static function csv_headers($filename = "download.csv", $modifyTime = 0, $expires = 0) {
    self::_common_headers($modifyTime, $expires);
    header("Content-Description: File Transfer");
    header("Content-disposition: attachment; filename=$filename");
    header("Content-type: text/csv");
  }

  public static function excel_headers($filename = "download.xml") {
    self::_common_headers();
    header("Content-Description: File Transfer");
    header("Content-Disposition: attachment; filename=$filename");
    header("Content-type: application/vnd.ms-excel");
  }

  public static function exe_headers($filename = null, $modifyTime = 0) {
    self::_common_headers($modifyTime);
    header("Content-type: application/octet-stream");
    if (is_null($filename))
      $filename = 'binary-' . date('Y-m-d') . '.bin';

    header(sprintf('Content-Disposition: attachment; filename="%s"', $filename));
  }

  public static function headers($mimetype, $modifyTime = 0, $expires = 0) {
    self::_common_headers($modifyTime, $expires);
    header(sprintf('Content-type: %s', $mimetype));
  }

  public static function html_docType() {
    $docType = (userAgent::isIE() ? 'html4' : 'html5');

    if ($docType == 'html5') {
      if (userAgent::isMobileDevice()) {
        return ("<!DOCTYPE html>\n<html lang=\"en\">");
      } else {
        return ("<!DOCTYPE html>\n<html class=\"desktop\" lang=\"en\">");
      }
    } else {
      return implode(PHP_EOL, [
        '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
        '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">'
      ]);
    }
  }

  public static function html_headers($charset = false) {
    if (!$charset)
      $charset = 'UTF-8';

    self::_common_headers();

    if (config::$CONTENT_SECURITY_ENABLED) {
      
      $policy = "frame-ancestors 'self';";
      if (config::$CONTENT_SECURITY_INCLUDE_CLOUDFLARE) {
        $policy = "script-src 'self' https://static.cloudflareinsights.com 'unsafe-inline'; frame-ancestors 'self';";
      }
      header("Content-Security-Policy: $policy");
    }

    if (config::$CONTENT_ENABLE_CROSS_ORIGIN_HEADER_WITH_PROTOCOL) {

      header(sprintf("Access-Control-Allow-Origin: %s", strings::url('', $protocol = true)));
    } elseif (config::$CONTENT_ENABLE_CROSS_ORIGIN_HEADER) {

      header(sprintf("Access-Control-Allow-Origin: %s", strings::url()));
    }

    header(sprintf("Content-type: text/html; charset=%s", $charset));
  }

  public static function gif_headers($modifyTime = 0, $expires = null) {

    if (is_null($expires)) $expires = config::$IMG_EXPIRE_TIME;

    self::_common_headers($modifyTime, $expires);
    header("Content-type: image/gif");
  }

  public static function icon_headers($modifyTime = 0, $expires = null) {

    if (is_null($expires)) $expires = config::$IMG_EXPIRE_TIME;

    self::_common_headers($modifyTime, $expires);
    header('Content-type: image/x-icon');
  }

  public static function javascript_headers($modifyTime = 0, $expires = 0) {
    self::_common_headers($modifyTime, $expires);
    header('X-Content-Type-Options: nosniff');
    header('Content-type: text/javascript');
  }

  public static function jpg_headers($modifyTime = 0, $expires = null) {

    if (is_null($expires)) $expires = config::$IMG_EXPIRE_TIME;

    self::_common_headers($modifyTime, $expires);
    header("Content-type: image/jpeg");
  }

  public static function json_headers($modifyTime = 0, $length = 0, $contentType = 'application/json') {
    self::_common_headers($modifyTime);
    header(sprintf('Content-type: %s; charset=utf-8', $contentType));
    if ($length) header(sprintf('Content-length: %s', $length));
  }

  public static function mso_docType() {
    return '<html xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns:m="http://schemas.microsoft.com/office/2004/12/omml" xmlns="http://www.w3.org/TR/REC-html40">';
  }

  public static function pdf_headers($filename = null, $modifyTime = 0) {
    self::_common_headers($modifyTime);
    header('Content-type: application/pdf');
    if (is_null($filename))
      $filename = 'pdf-' . date('Y-m-d') . '.pdf';

    header(sprintf('Content-Disposition: inline; filename="%s"', $filename));
  }

  public static function png_headers($modifyTime = 0, $expires = null) {

    if (is_null($expires)) $expires = config::$IMG_EXPIRE_TIME;

    self::_common_headers($modifyTime, $expires);
    header("Content-type: image/png");
  }

  public static function redirect($url = null, $message = "", $auto = true) {

    if (is_null($url)) {
      $url = \url::$URL;  // default

    } elseif (!(preg_match('@^(http|//)@i', (string)$url))) {
      if ('/' != $url)
        $url = \url::$URL . $url;
    }

    if ($message == "") {
      header(sprintf('location: %s', $url));
      exit;
    }

    $p = new \dvc\pages\bootstrap4;
    $p->title = $message;
    $p->footer = false;
    if (userAgent::isMobileDevice())
      $p->meta[] = '<meta name="viewport" content="initial-scale=1" />';

    if ($auto) {
      $p->header(false);
      printf(
        '<meta http-equiv="refresh" content="1; url=%s" />%s</head><body>',
        $url,
        PHP_EOL
      );
    } else {
      $p->header();
    }

    printf('<div style="margin: 50px auto 10px auto; padding: 10px; border: 1px solid silver; max-width: 600px;">
      <p style="margin-top: 15px; margin-bottom: 15px;">%s</p>

		  <div style="text-align: right; padding-right: 20px;">
			  <a style="text-decoration: none; font-style: italic;" href="%s">%s .... .</a>
		  </div>
    </div>', $message,  $url, ($auto ? 'redirecting' : 'continue'));

    exit;  // don't run anything else

  }

  public static function serve($path, array $options = []): void {

    $debug = false;
    // $debug = true;

    if (file_exists($path)) {

      $serve = [
        'avi' => 'video/x-msvideo',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'map' => 'text/plain',
        'mp4' => 'video/mp4',
        'mov' => 'video/quicktime',
        'txt' => 'text/plain',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
      ];

      $path_parts = pathinfo($path);
      $mimetype = mime_content_type($path);

      if ($debug) logger::debug(sprintf('<%s> %s', $mimetype, __METHOD__));

      if ('application/pdf' == $mimetype) {

        self::pdf_headers($path_parts['basename'], filemtime($path));
        readfile($path);
        if ($debug) logger::debug(sprintf('<served: %s> %s', $path, __METHOD__));
      } elseif ('image/jpeg' == $mimetype) {

        if (strstr($path, url::$URL . 'images/')) {

          self::jpg_headers(filemtime($path), config::$CORE_IMG_EXPIRE_TIME);
        } else {

          self::jpg_headers(filemtime($path));
        }
        readfile($path);
        if ($debug) logger::debug(sprintf('<served: %s> %s', $path, __METHOD__));
      } elseif ('video/' == substr($mimetype, 0, 6) && ($options['stream'] ?? null)) {

        $content = file_get_contents($path);
        $size = strlen($content);
        $start = 0;
        $end = $size - 1;

        header(sprintf('Content-Type: %s', $mimetype));
        header('Accept-Ranges: bytes');
        header('Connection: close');

        $rangeHeader = $_SERVER['HTTP_RANGE'] ?? null;
        if ($rangeHeader && preg_match('/bytes=(\d*)-(\d*)/', $rangeHeader, $m)) {

          if ($m[1] !== '') $start = (int)$m[1];
          if ($m[2] !== '') $end = (int)$m[2];

          if ($start > $end || $start > $size - 1) {
            header('HTTP/1.1 416 Range Not Satisfiable');
            header("Content-Range: bytes */$size");
            return;
          }

          $length = $end - $start + 1;
          header('HTTP/1.1 206 Partial Content');
          header("Content-Range: bytes $start-$end/$size");
          header("Content-Length: $length");

          // turn off output buffering to avoid extra bytes and reduce latency
          while (ob_get_level()) ob_end_clean();

          $chunkSize = 8192;
          $pos = $start;
          while ($pos <= $end) {
            $read = min($chunkSize, $end - $pos + 1);
            echo substr($content, $pos, $read);
            flush();
            $pos += $read;
          }
        } else {

          // no range requested - send entire content
          header("Content-Length: $size");
          while (ob_get_level()) ob_end_clean();
          echo $content;
        }
      } elseif (isset($path_parts['extension'])) {

        $ext = strtolower($path_parts['extension']);

        if ($ext == 'css') {

          self::css_headers(filemtime($path));
          readfile($path);
          if ($debug) logger::debug(sprintf('<served: %s> %s', $path, __METHOD__));
        } elseif ($ext == 'csv') {

          self::csv_headers($path_parts['basename'], filemtime($path));
          readfile($path);
          if ($debug) logger::debug(sprintf('<served: %s> %s', $path, __METHOD__));
        } elseif ($ext == 'eml') {

          self::headers('application/octet-stream', filemtime($path));
          header(sprintf('Content-Disposition: attachment; filename="%s"', $path_parts['basename']));
          readfile($path);
          if ($debug) logger::debug(sprintf('<served: %s> %s', $path, __METHOD__));
        } elseif ($ext == 'eot') {

          self::headers('application/vnd.ms-fontobject', filemtime($path), config::$FONT_EXPIRE_TIME);
          readfile($path);
          if ($debug) logger::debug(sprintf('<served: %s> %s', $path, __METHOD__));
        } elseif ($ext == 'ico') {

          self::icon_headers(filemtime($path), config::$CORE_IMG_EXPIRE_TIME);
          readfile($path);
          if ($debug) logger::debug(sprintf('<served: %s> %s', $path, __METHOD__));
        } elseif ($ext == 'gif') {

          if (strstr($path, url::$URL . 'images/')) {
            self::gif_headers(filemtime($path), config::$CORE_IMG_EXPIRE_TIME);
          } else {
            self::gif_headers(filemtime($path));
          }
          readfile($path);
          if ($debug) logger::debug(sprintf('<served: %s> %s', $path, __METHOD__));
        } elseif ($ext == 'jpg' || $ext == 'jpeg') {

          if (strstr($path, url::$URL . 'images/')) {
            self::jpg_headers(filemtime($path), config::$CORE_IMG_EXPIRE_TIME);
          } else {
            self::jpg_headers(filemtime($path));
          }
          readfile($path);
          if ($debug) logger::debug(sprintf('<served: %s> %s', $path, __METHOD__));
        } elseif ($ext == 'js') {

          $expires = 0;
          if (strstr($path, 'jquery-')) {

            $expires = config::$JQUERY_EXPIRE_TIME;
          } elseif (strstr($path, 'inputosaurus.js')) {

            $expires = config::$JQUERY_EXPIRE_TIME;
          } elseif (strstr($path, 'tinylib.js')) {

            $expires = config::$JQUERY_EXPIRE_TIME;
          } elseif (strstr($path, 'moment.min.js')) {

            $expires = config::$JQUERY_EXPIRE_TIME;
          } elseif (strstr($path, 'bootstrap.min.js')) {

            $expires = config::$JQUERY_EXPIRE_TIME;
          } elseif (strstr($path, 'brayworthlib.js')) {

            $expires = config::$JQUERY_EXPIRE_TIME;
          } elseif (strings::endswith($path, '.js')) {

            $expires = config::$JS_EXPIRE_TIME;
          }

          self::javascript_headers(filemtime($path), $expires);
          readfile($path);
          if ($debug) logger::debug(sprintf('<served: %s> %s', $path, __METHOD__));
        } elseif ($ext == 'json') {

          if (basename($path) == 'importmap.json') {

            self::json_headers(filemtime($path), filesize($path), 'application/importmap+json');
          } else {

            self::json_headers(filemtime($path));
          }
          readfile($path);
          if ($debug) logger::debug(sprintf('<served: %s> %s', $path, __METHOD__));
        } elseif ($ext == 'html') {

          self::html_headers($path_parts['basename'], filemtime($path));
          readfile($path);
          if ($debug) logger::debug(sprintf('<served: %s> %s', $path, __METHOD__));
        } elseif ($ext == 'pdf') {

          self::pdf_headers($path_parts['basename'], filemtime($path));
          readfile($path);
          if ($debug) logger::debug(sprintf('<served: %s> %s', $path, __METHOD__));
        } elseif ($ext == 'png') {

          if (strstr($path, url::$URL . 'images/')) {

            self::png_headers(filemtime($path), config::$CORE_IMG_EXPIRE_TIME);
          } else {

            self::png_headers(filemtime($path));
          }

          readfile($path);
          if ($debug) logger::debug(sprintf('<served: %s> %s', $path, __METHOD__));
        } elseif ($ext == 'svg') {

          /**
           * maybe the expire time is like javascript rather than images - this is conservative
           */
          self::headers('image/svg+xml', filemtime($path), config::$JS_EXPIRE_TIME);
          readfile($path);
          if ($debug) logger::debug(sprintf('<served: %s> %s', $path, __METHOD__));
        } elseif ($ext == 'tif' || $ext == 'tiff') {

          self::tiff_headers($path_parts['basename'], filemtime($path));
          readfile($path);
          if ($debug) logger::debug(sprintf('<served: %s> %s', $path, __METHOD__));
        } elseif ($ext == 'ttf' || $ext == 'otf') {

          self::headers('application/font-sfnt', filemtime($path), config::$FONT_EXPIRE_TIME);
          readfile($path);
          if ($debug) logger::debug(sprintf('<served: %s> %s', $path, __METHOD__));
        } elseif ($ext == 'woff' || $ext == 'woff2') {

          self::headers('application/font-woff', filemtime($path), config::$FONT_EXPIRE_TIME);
          readfile($path);
          if ($debug) logger::debug(sprintf('<served: %s> %s', $path, __METHOD__));
        } elseif ($ext == 'xml') {

          self::xml_headers(filemtime($path));
          readfile($path);
          if ($debug) logger::debug(sprintf('<served: %s> %s', $path, __METHOD__));
        } elseif ($ext == 'zip') {

          self::zip_headers($path_parts['basename'], filemtime($path));
          header("Content-Length: " . filesize($path));
          ob_flush();
          ob_end_flush();
          readfile($path);
          if ($debug) logger::debug(sprintf('<served: %s> %s', $path, __METHOD__));
        } elseif (isset($serve[$ext])) {

          self::headers($serve[$ext], filemtime($path));
          readfile($path);
          if ($debug) logger::debug(sprintf('<served %s from %s> %s', $serve[$ext], $path, __METHOD__));
        } elseif ($debug) {

          logger::debug(sprintf('<not serving (file type not served): %s> %s', $path, __METHOD__));
        }
      } else {

        logger::info(sprintf('<not serving : %s> %s', $path, __METHOD__));
      }
    } elseif ($debug) {

      logger::info(sprintf('<not serving (not found): %s> %s', $path, __METHOD__));
    }
  }

  /**
   * serveBootStrap($type = 'css', $fontFile = null) : void
   *
   * @param string $type
   * @param string $fontFile
   *
   * @return void
   */
  public static function serveBootStrap(string $type = 'css', string|null $fontFile = null): void {

    // self::logger( sprintf('<%s> %s', $type, __METHOD__));
    if ('icons' == $type) {

      // if ($lib = realpath(__DIR__ . '/css/bootstrap-icons/bootstrap-icons.css')) {
      if ($lib = resources::bootstrapIcons('css')) {

        self::serve($lib);
      } elseif ($lib = realpath(self::_twbs_dir() . '/bootstrap-icons/font/bootstrap-icons.css')) {

        logger::deprecated(sprintf('<%s> %s', $lib, __METHOD__));
        self::serve($lib);
      } else {

        logger::info(sprintf('<cannot locate bootstrap_font_css_file> %s', __METHOD__));
      }
    } elseif ('fonts' == $type) {

      if (\in_array($fontFile, [
        'bootstrap-icons.woff',
        'bootstrap-icons.woff2'
      ])) {

        // if ($lib = realpath(__DIR__ . '/css/bootstrap-icons/fonts/' . $fontFile)) {
        if ($lib = resources::bootstrapIcons('fonts', $fontFile)) {

          self::serve($lib);
        } elseif ($lib = realpath(self::_twbs_dir() . '/bootstrap-icons/font/fonts/' . $fontFile)) {

          logger::deprecated(sprintf('<%s> %s', $lib, __METHOD__));
          self::serve($lib);
        } else {
          logger::info(sprintf('<cannot locate bootstrap_font_file> %s', __METHOD__));
        }
        // self::logger(realpath( __DIR__ . '/../../vendor/twbs/bootstrap-icons/font/'));
      }
    } elseif (config::$BOOTSTRAP_REQUIRE_POPPER) {

      logger::deprecated('$BOOTSTRAP_REQUIRE_POPPER');

      if ('css' == $type) {

        $lib = __DIR__ . '/css/bootstrap4/bootstrap.min.css';
        self::serve($lib);
      } elseif ('js' == $type) {

        $files = __DIR__ . '/js/bootstrap4/bootstrap.bundle.min.js';

        jslib::viewjs([
          'debug' => false,
          'libName' => 'bootstrap4',
          'jsFiles' => $files,
          'libFile' => config::tempdir()  . '_bootstrap4_tmp.js'
        ]);
      }
    } elseif ('css' == $type) {

      $themeFile = __DIR__ . '/css/bootstrap4/bootstrap.min.css';
      if ('blue' == currentUser::option('theme')) {

        $themeFile = __DIR__ . '/css/bootstrap4/bootstrap-blue.min.css';
      } elseif ('orange' == currentUser::option('theme')) {

        $themeFile = __DIR__ . '/css/bootstrap4/bootstrap-orange.min.css';
      } elseif ('pink' == currentUser::option('theme')) {

        $themeFile = __DIR__ . '/css/bootstrap4/bootstrap-pink.min.css';
      } elseif ('blue' == \config::$THEME) {

        $themeFile = __DIR__ . '/css/bootstrap4/bootstrap-blue.min.css';
      } elseif ('orange' == \config::$THEME) {

        $themeFile = __DIR__ . '/css/bootstrap4/bootstrap-orange.min.css';
      } elseif ('pink' == \config::$THEME) {

        $themeFile = __DIR__ . '/css/bootstrap4/bootstrap-pink.min.css';
      }

      if ($lib = realpath($themeFile)) {

        self::serve($lib);
      } else {

        // logger::info(sprintf('<cannot locate bootstrap_css_file> <%s> %s', __DIR__, __METHOD__));
        logger::info(sprintf('<cannot locate bootstrap_css_file> <%s> %s', $themeFile, __METHOD__));
      }
    } elseif ('js' == $type) {

      if ($lib = realpath(__DIR__ . '/js/bootstrap4/bootstrap.bundle.min.js')) {

        self::serve($lib);
      } else {

        logger::info(sprintf('<cannot locate bootstrap.bundle.min.js> %s', __METHOD__));
      }
    } else {

      logger::info(sprintf('<%s> %s', $type, __METHOD__));
    }
  }

  public static function serveBootStrap5($type = 'css', string|null $theme = null): void {

    if ('css' == $type) {

      if (!$theme) $theme = currentUser::option('theme');
      if (!$theme) $theme = config::$THEME;

      $lib = match ($theme) {
        'blue' => __DIR__ . '/css/bootstrap5/bootstrap-blue.min.css',
        'orange' => __DIR__ . '/css/bootstrap5/bootstrap-orange.min.css',
        'pink' => __DIR__ . '/css/bootstrap5/bootstrap-pink.min.css',
        default => resources::bootstrap5('css')
      };

      // logger::info(sprintf('<%s> %s', $lib, __METHOD__));
      self::serve($lib);
    } elseif ('polyfill' == $type) {

      $lib = __DIR__ . '/resource/bootstrap4-5.polyfill.css';
      self::serve($lib);
    } elseif ('js' == $type) {

      // $lib = __DIR__ . '/js/bootstrap5/bootstrap.bundle.min.js';
      $lib = resources::bootstrap5('js');
      self::serve($lib);
    }
  }

  public static function serveMermaid($type = 'css'): void {

    if ('css' == $type) {

      // $lib = __DIR__ . '/resources/mermaid/mermaid.min.css';
      // self::serve($lib);
    } elseif ('js' == $type) {

      $lib = __DIR__ . '/resources/mermaid/mermaid.min.js';
      self::serve($lib);
    }
  }

  public static function serveQuill($type = 'css'): void {

    if ('css' == $type) {

      $lib = config::tempdir() . '/_quill_.css';

      /**
       * check if the lib file exists or if any of the files in
       * __DIR__ . '/resources/quill/*.css'
       * are more recent than $lib and if so - create/make the lib
       */
      $create = true;
      if (file_exists($lib)) {

        $libtime = filemtime($lib);
        $create = false;

        // check if it is older than 30 minutes
        if (time() - $libtime > 1800) {

          $create = true;
        } else {

          foreach (glob(__DIR__ . '/resources/quill/*.css') as $f) {
            // check the time againt the modtime for lib
            if (filemtime($f) > $libtime) {
              $create = true;
              break;
            }
          }
        }
      }

      // $create = true;
      if ($create) {

        // get all css files in __DIR__ . '/resources/css/'
        $files = glob(__DIR__ . '/resources/quill/*.css');
        // sort and ensure quill.snow.css is first
        usort($files, function ($a, $b) {
          if (basename($a) == 'quill.snow.css') return -1;
          if (basename($b) == 'quill.snow.css') return 1;
          return 0;
        });

        // logger::dump($files, logger::caller());
        $css = array_map(fn($f) => file_get_contents($f), $files);

        // bind them to gether and make a single css file $lib; unlink the file first
        if (file_exists($lib)) unlink($lib);
        file_put_contents($lib, implode("\n", $css));
      }

      self::serve($lib);
    } elseif ('js' == $type) {

      $lib = config::tempdir() . '/_quill_.js';
      /**
       * check if the lib file exists or if any of the files in
       * __DIR__ . '/resources/quill/*.js'
       * are more recent than $lib and if so - create/make the lib
       */

      $create = true;
      if (file_exists($lib)) {

        $libtime = filemtime($lib);
        $create = false;

        // check if it is older than 30 minutes
        if (time() - $libtime > 1800) {

          $create = true;
        } else {

          foreach (glob(__DIR__ . '/resources/quill/*.js') as $f) {
            // check the time againt the modtime for lib
            if (filemtime($f) > $libtime) {
              $create = true;
              break;
            }
          }
        }
      }

      // $create = true;
      if ($create) {

        $files = glob(__DIR__ . '/resources/quill/*.js');
        // sort and ensure quill.js is first
        usort($files, function ($a, $b) {
          if (basename($a) == 'quill.js') return -1;
          if (basename($b) == 'quill.js') return 1;
          return 0;
        });
        $js = array_map(fn($f) => file_get_contents($f), $files);

        // bind them to gether and make a single css file $lib; unlink the file first
        if (file_exists($lib)) unlink($lib);
        file_put_contents($lib, implode("\n", $js));
      }

      self::serve($lib);
    }
  }

  public static function serveToastUI($type = 'css'): void {

    if ('css' == $type) {

      $lib = __DIR__ . '/resources/toastui/toastui-editor.min.css';

      self::serve($lib);
    } elseif ('js' == $type) {

      $lib = __DIR__ . '/resources/toastui/toastui-editor-all.min.js';
      self::serve($lib);
    }
  }

  public static function text_headers($modifyTime = 0, $expires = 0) {
    self::_common_headers($modifyTime, $expires);
    header("Content-type: text/plain");
  }

  public static function tiff_headers($filename = null, $modifyTime = 0) {
    self::_common_headers($modifyTime);
    header("Content-type: image/tiff");
    if (is_null($filename))
      $filename = 'binary-' . date('Y-m-d') . '.tiff';

    header(sprintf('Content-Disposition: inline; filename="%s"', $filename));
  }

  public static function xml_docType() {
    return ("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" . self::html_docType());
  }

  public static function xml_headers($modifyTime = 0) {
    self::_common_headers($modifyTime);
    header('Content-type: text/xml');
  }

  public static function zip_headers($filename = null, $modifyTime = 0) {
    self::_common_headers($modifyTime);
    header("Content-type: application/zip");
    if (is_null($filename))
      $filename = 'binary-' . date('Y-m-d') . '.zip';

    header(sprintf('Content-Disposition: attachment; filename="%s"', $filename));
  }
}
