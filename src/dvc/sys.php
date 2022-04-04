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

abstract class sys {
  protected static $_loglevel = 1;
  protected static $_dbi = null;

  static $debug = false;

  protected static function _twbs_dir() {
    $dir = realpath(__DIR__ . '/../../../../twbs');
    if (!$dir) {
      $dir = realpath(__DIR__ . '/../../vendor/twbs');
    }

    return $dir;
  }

  public static function bootstrap_icon_dir() {
    if ($dir = self::_twbs_dir()) {
      return $dir . '/bootstrap-icons/icons/';
    }

    return '';
  }

  public static function bootStrap_version(): \stdClass {
    $ret = (object)[
      'version' => 0,
      'short' => 0,
      'major' => 0,

    ];

    if ($root = self::_twbs_dir()) {
      $path = realpath(
        implode(
          DIRECTORY_SEPARATOR,
          [
            $root,
            'bootstrap',
            'package.json'

          ]

        )

      );

      if (\file_exists($path)) {
        if ($j = @json_decode(\file_get_contents($path))) {
          $ret->version = $j->version;

          $short = $j->version;
          if (isset($j->version_short)) {
            $ret->short = $j->version_short;
          } elseif (isset($j->config->version_short)) {
            $ret->short = $j->config->version_short;
          };

          $ret->major = (int)$short;
        }
      }
    }

    return $ret;
  }

  public static function dbi() {
    if (is_null(self::$_dbi)) {
      if ('sqlite' == \config::$DB_TYPE) {
        self::$_dbi = sqlite\db::instance();
      } else {
        self::$_dbi = new dbi;
      }
    }

    return (self::$_dbi);
  }

  public static function dbCheck(string $file) {
    return 'sqlite' == \config::$DB_TYPE ?
      new sqlite\dbCheck(self::dbi(), $file) :
      new \dao\dbCheck(self::dbi(), $file);
  }

  public static function dbCachePrefix() {
    if (\config::$DB_CACHE_PREFIX) {
      return \config::$DB_CACHE_PREFIX;
    } elseif ('mysql' == \config::$DB_TYPE) {
      return str_replace('.', '_', \config::$DB_HOST . '_' . \config::$DB_NAME);
    } else {
      /**
       * it's probably sqlite, so we need a unique prefix for this database
       *
       * this could require further development if we are going to support
       * multiple cached sqlite databases in the same application, otherwise
       * this database, this appication is unique
       * */
      $path = implode(DIRECTORY_SEPARATOR, [
        \config::dataPath(),
        'dbCachePrefix.json'

      ]);

      if (\file_exists($path)) {
        $j = \json_decode(\file_get_contents($path));
        \config::$DB_CACHE_PREFIX = $j->prefix;
        return \config::$DB_CACHE_PREFIX;
      } else {
        $a = (object)['prefix' => bin2hex(random_bytes(6))];
        \file_put_contents($path, \json_encode($a));
        \config::$DB_CACHE_PREFIX = $a->prefix;
        return \config::$DB_CACHE_PREFIX;
      }
    }
  }

  public static function diskspace() {
    $ret = (object)[
      'free' => disk_free_space(__DIR__),
      'total' => disk_total_space(__DIR__),
      'threshhold' => \config::$FREE_DISKSPACE_THRESHHOLD

    ];

    $ret->exceeded = $ret->free < $ret->threshhold;

    return $ret;
  }

  public static function dump($v, $title = '', $lExit = true) {
    if (!$title) {
      if (gettype($v) == 'object')
        $title = get_class($v);
      else
        $title = gettype($v);
    }

    if ($title == 'dvc\dbResult' || $title == 'dvc\sqlite\dbResult') {
      printf('<h1>%s</h1>', $title);
      while ($r = $v->dto())
        new html\dump($r, get_class($r));
    } else {
      new html\dump($v, $title);
    }

    if ($lExit)
      exit;
  }

  public static function getTemplate($template) {
    if ($template) {
      if ($template = preg_replace('/[^\da-z_]/i', '', $template)) {
        $template .= '.html';

        $path = sprintf(
          '%s%sapp%stemplates%s%s',
          \application::app()->getRootPath(),
          DIRECTORY_SEPARATOR,
          DIRECTORY_SEPARATOR,
          DIRECTORY_SEPARATOR,
          $template
        );

        if (file_exists($path)) {
          \sys::serve($path);
        } else {
          $path = sprintf(
            '%s%stemplates%s%s',
            __DIR__,
            DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR,
            $template
          );

          if (file_exists($path))
            \sys::serve($path);
        }
      }
    }
  }

  public static function isWindows() {
    return ('WIN' === strtoupper(substr(PHP_OS, 0, 3)));
  }

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

  public static function logger($v, $level = 0) {
    if ((int)self::$_loglevel > 0 && $level <= (int)self::$_loglevel) {
      error_log($v);
    }
  }

  public static function logloaderon($b) {
    error_log(sprintf('deprecated : %s', self::traceCaller()));
  }

  public static function loaderCounter(core\hitter $hitter) {
    error_log(sprintf('deprecated : %s', self::traceCaller()));
  }

  public static function logloader($v) {
    error_log(sprintf('deprecated : %s : %s', $v, self::traceCaller()));
  }

  public static function logSQL($v, $level = 0) {
    self::logger(preg_replace(["@\r\n@", "@\n@", "@\t@", "@\s\s*@"], ' ', $v));
  }

  public static function mailer() {
    $mail = new \PHPMailer;
    $mail->XMailer = 'BrayWorth DVC Mailer 1.0.0 (https://brayworth.com/)';

    if (self::isWindows()) {
      $mail->isSMTP(); // use smtp with server set to mail

      $mail->Host = 'mail';
      $mail->Port = 25;
      $mail->SMTPSecure = 'tls';
      $mail->SMTPOptions = [
        'ssl' => [
          'verify_peer' => false,
          'verify_peer_name' => false,
          'allow_self_signed' => true

        ]

      ];
    }

    $mailconfig = sprintf(
      '%s/mail-config.json',
      rtrim(\config::dataPath(), '/ ')

    );

    if (file_exists($mailconfig)) {
      $_mc = json_decode(file_get_contents($mailconfig));

      if (isset($_mc->Host)) {
        $mail->isSMTP(); // use smtp with server set to mail
        $mail->Host = $_mc->Host;
      }

      if (isset($_mc->Port)) $mail->Port = $_mc->Port;

      if (isset($_mc->SMTPSecure)) $mail->SMTPSecure = $_mc->SMTPSecure;

      if (isset($_mc->SMTPOptions)) {
        if (isset($_mc->SMTPOptions->ssl)) {
          $mail->SMTPOptions = [
            'ssl' => (array)$_mc->SMTPOptions->ssl

          ];
        }
      }

      if (isset($_mc->SMTPUserName) && isset($_mc->SMTPPassword)) {
        $mail->SMTPAuth = true;
        $mail->Username = $_mc->SMTPUserName;
        $mail->Password = $_mc->SMTPPassword;
      }
    } else {

      $mailconfig = sprintf(
        '%s/mail-config-sample.json',
        rtrim(\config::dataPath(), '/ ')
      );

      file_put_contents(
        $mailconfig,
        json_encode((object)[
          'Host' => $mail->Host,
          'Port' => $mail->Port,
          'SMTPSecure' => $mail->SMTPSecure,
          'SMTPOptions' => $mail->SMTPOptions
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)

      );
    }

    $mail->setFrom(\config::$SUPPORT_EMAIL, \config::$SUPPORT_NAME);
    return ($mail);
  }

  protected static $_options = [];

  protected static function _options_file() {
    return implode(DIRECTORY_SEPARATOR, [
      rtrim(config::dataPath(), '/ '),
      'sys.json'

    ]);
  }

  public static function option($key, $val = null) {
    $debug = false;
    // $debug = true;

    if (!self::$_options) {
      if (file_exists($config = self::_options_file())) {
        self::$_options = (array)json_decode(file_get_contents($config));
      }
    }

    $ret = '';
    if (self::$_options) {
      /* return the existing value */
      if (isset(self::$_options[$key])) {
        $ret = (string)self::$_options[$key];

        if ($debug) self::logger(sprintf('retrieve option value : %s = %s', $key, $ret));
      } elseif ($debug) {
        self::logger(sprintf('retrieve option value (default - not set) : %s = %s', $key, $ret));
      }
    } elseif ($debug) {
      self::logger(sprintf('retrieve option value (null): %s = %s', $key, $ret));
    }


    if (!is_null($val)) {

      /* writer */
      if ((string)$val == '') {
        if (isset(self::$_options[$key])) {
          unset(self::$_options[$key]);
        }
      } else {
        self::$_options[$key] = (string)$val;
      }

      file_put_contents(
        self::_options_file(),
        json_encode(
          self::$_options,
          JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT

        )

      );
    }

    return ($ret);
  }

  public static function serve($path) {
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

      if (self::$debug) \sys::logger(sprintf('<%s> %s', $mimetype, __METHOD__));

      if ('image/jpeg' == $mimetype) {
        if (strstr($path, url::$URL . 'images/')) {
          Response::jpg_headers(filemtime($path), \config::$CORE_IMG_EXPIRE_TIME);
        } else {
          Response::jpg_headers(filemtime($path));
        }
        readfile($path);
        if (self::$debug) \sys::logger("served: $path");
      } elseif (isset($path_parts['extension'])) {
        $ext = strtolower($path_parts['extension']);

        if ($ext == 'css') {
          Response::css_headers(filemtime($path));
          readfile($path);
          if (self::$debug) \sys::logger("served: $path");
        } elseif ($ext == 'js') {
          $expires = 0;
          if (strstr($path, 'jquery-'))
            $expires = \config::$JQUERY_EXPIRE_TIME;
          elseif (strstr($path, 'inputosaurus.js'))
            $expires = \config::$JQUERY_EXPIRE_TIME;
          elseif (strstr($path, 'tinylib.js'))
            $expires = \config::$JQUERY_EXPIRE_TIME;
          elseif (strstr($path, 'moment.min.js'))
            $expires = \config::$JQUERY_EXPIRE_TIME;
          elseif (strstr($path, 'bootstrap.min.js'))
            $expires = \config::$JQUERY_EXPIRE_TIME;
          elseif (strstr($path, 'brayworthlib.js'))
            $expires = \config::$JQUERY_EXPIRE_TIME;
          elseif (strings::endswith($path, '.js'))
            $expires = \config::$JS_EXPIRE_TIME;

          Response::javascript_headers(filemtime($path), $expires);
          readfile($path);
          if (self::$debug) \sys::logger("served: $path");
        } elseif ($ext == 'eml') {
          Response::headers('application/octet-stream', filemtime($path));
          readfile($path);
          if (self::$debug) \sys::logger("served: $path");
        } elseif ($ext == 'eot') {
          Response::headers('application/vnd.ms-fontobject', filemtime($path), \config::$FONT_EXPIRE_TIME);
          readfile($path);
          if (self::$debug) \sys::logger("served: $path");
        } elseif ($ext == 'ico') {
          Response::icon_headers(filemtime($path), \config::$CORE_IMG_EXPIRE_TIME);
          readfile($path);
          if (self::$debug) \sys::logger("served: $path");
        } elseif ($ext == 'png') {
          if (strstr($path, url::$URL . 'images/'))
            Response::png_headers(filemtime($path), \config::$CORE_IMG_EXPIRE_TIME);
          else
            Response::png_headers(filemtime($path));

          readfile($path);
          if (self::$debug) \sys::logger("served: $path");
        } elseif ($ext == 'ttf' || $ext == 'otf') {
          Response::headers('application/font-sfnt', filemtime($path), \config::$FONT_EXPIRE_TIME);
          readfile($path);
          if (self::$debug) \sys::logger("served: $path");
        } elseif ($ext == 'woff' || $ext == 'woff2') {
          Response::headers('application/font-woff', filemtime($path), \config::$FONT_EXPIRE_TIME);
          readfile($path);
          if (self::$debug) \sys::logger("served: $path");
        } elseif ($ext == 'jpg' || $ext == 'jpeg') {
          if (strstr($path, url::$URL . 'images/')) {
            Response::jpg_headers(filemtime($path), \config::$CORE_IMG_EXPIRE_TIME);
          } else {
            Response::jpg_headers(filemtime($path));
          }
          readfile($path);
          if (self::$debug) \sys::logger("served: $path");
        } elseif ($ext == 'gif') {
          if (strstr($path, url::$URL . 'images/')) {
            Response::gif_headers(filemtime($path), \config::$CORE_IMG_EXPIRE_TIME);
          } else {
            Response::gif_headers(filemtime($path));
          }
          readfile($path);
          if (self::$debug) \sys::logger("served: $path");
        } elseif ($ext == 'svg') {
          /*
					* maybe the expire time is like javascript rather than images - this is conservative */
          Response::headers('image/svg+xml', filemtime($path), \config::$JS_EXPIRE_TIME);
          readfile($path);
          if (self::$debug) \sys::logger("served: $path");
        } elseif ($ext == 'json') {
          Response::json_headers(filemtime($path));
          readfile($path);
          if (self::$debug) \sys::logger("served: $path");
        } elseif ($ext == 'xml') {
          Response::xml_headers(filemtime($path));
          readfile($path);
          if (self::$debug) \sys::logger("served: $path");
        } elseif ($ext == 'csv') {
          Response::csv_headers($path_parts['basename'], filemtime($path));
          readfile($path);
          if (self::$debug) \sys::logger(sprintf('served: %s', $path));
        } elseif ($ext == 'pdf') {
          Response::pdf_headers($path_parts['basename'], filemtime($path));
          readfile($path);
          if (self::$debug) \sys::logger(sprintf('served: %s', $path));
        } elseif ($ext == 'tif' || $ext == 'tiff') {
          Response::tiff_headers($path_parts['basename'], filemtime($path));
          readfile($path);
          if (self::$debug) \sys::logger(sprintf('served: %s', $path));
        } elseif ($ext == 'zip') {
          Response::zip_headers($path_parts['basename'], filemtime($path));
          readfile($path);
          if (self::$debug) \sys::logger(sprintf('served: %s', $path));
        } elseif ($ext == 'html') {
          Response::html_headers($path_parts['basename'], filemtime($path));
          readfile($path);
          if (self::$debug) \sys::logger(sprintf('served: %s', $path));
        } elseif (isset($serve[$ext])) {
          Response::headers($serve[$ext], filemtime($path));
          readfile($path);
          if (self::$debug) \sys::logger(sprintf('served %s from %s', $serve[$ext], $path));
        } elseif (self::$debug) {
          \sys::logger(sprintf('not serving (file type not served): %s', $path));
        }
      } else {
        \sys::logger(sprintf('not serving : %s', $path));
      }
    } elseif (self::$debug) {
      \sys::logger(sprintf('not serving (not found): %s', $path));
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
  public static function serveBootStrap(string $type = 'css', string $fontFile = null): void {

    // self::logger( sprintf('<%s> %s', $type, __METHOD__));
    if ('icons' == $type) {
      if ($lib = realpath(self::_twbs_dir() . '/bootstrap-icons/font/bootstrap-icons.css')) {
        self::serve($lib);
      } else {
        \sys::logger(sprintf('<cannot locate bootstrap_font_css_file> %s', __METHOD__));
      }
    } elseif ('fonts' == $type) {
      if (\in_array($fontFile, [
        'bootstrap-icons.woff',
        'bootstrap-icons.woff2'

      ])) {
        if ($lib = realpath(self::_twbs_dir() . '/bootstrap-icons/font/fonts/' . $fontFile)) {
          // self::logger($lib);
          self::serve($lib);
        } else {
          \sys::logger(sprintf('<cannot locate bootstrap_font_file> %s', __METHOD__));
        }
        // self::logger(realpath( __DIR__ . '/../../vendor/twbs/bootstrap-icons/font/'));
      }
    } elseif (\config::$BOOTSTRAP_REQUIRE_POPPER) {
      \sys::logger(sprintf('deprecated : $BOOTSTRAP_REQUIRE_POPPER is deprecated : %s', __FILE__));

      if ('css' == $type) {
        $lib = __DIR__ . '/bootstrap4/css/bootstrap.min.css';
        self::serve($lib);
      } elseif ('js' == $type) {
        $files = [
          __DIR__ . '/bootstrap4/js/bootstrap.js',
          __DIR__ . '/bootstrap4/js/popper.js',

        ];

        jslib::viewjs([
          'debug' => false,
          'libName' => 'bootstrap4',
          'jsFiles' => $files,
          'libFile' => \config::tempdir()  . '_bootstrap4_tmp.js'

        ]);
      }
    } elseif ('css' == $type) {
      $themeFile = __DIR__ . '/resource/bootstrap.min.css';
      if ('blue' == \currentUser::option('theme')) {
        $themeFile = __DIR__ . '/resource/bootstrap-blue.min.css';
      } elseif ('orange' == \currentUser::option('theme')) {
        $themeFile = __DIR__ . '/resource/bootstrap-orange.min.css';
      } elseif ('pink' == \currentUser::option('theme')) {
        $themeFile = __DIR__ . '/resource/bootstrap-pink.min.css';
      } elseif ('blue' == \config::$THEME) {
        $themeFile = __DIR__ . '/resource/bootstrap-blue.min.css';
      } elseif ('orange' == \config::$THEME) {
        $themeFile = __DIR__ . '/resource/bootstrap-orange.min.css';
      } elseif ('pink' == \config::$THEME) {
        $themeFile = __DIR__ . '/resource/bootstrap-pink.min.css';
      }
      if ($lib = realpath($themeFile)) {
        self::serve($lib);
      } else {
        // \sys::logger(sprintf('<cannot locate bootstrap_css_file> <%s> %s', __DIR__, __METHOD__));
        \sys::logger(sprintf('<cannot locate bootstrap_css_file> <%s> %s', $themeFile, __METHOD__));
      }
    } elseif ('js' == $type) {
      if ($lib = realpath(self::_twbs_dir() . '/bootstrap/dist/js/bootstrap.bundle.min.js')) {
        // \sys::logger(sprintf('<%s> %s', $lib, __METHOD__));
        self::serve($lib);
      } else {
        \sys::logger(sprintf('<cannot locate bootstrap_css_file> %s', __METHOD__));
      }
    } elseif ('bootstrap.bundle.min.js.map' == $type) {
      if ($lib = realpath(self::_twbs_dir() . '/bootstrap/dist/js/bootstrap.bundle.min.js.map')) {
        // \sys::logger(sprintf('<%s> %s', $lib, __METHOD__));
        self::serve($lib);
      } else {
        \sys::logger(sprintf('<cannot locate bootstrap_js_map> %s', __METHOD__));
      }
    } else {
      \sys::logger(sprintf('<%s> %s', $type, __METHOD__));
    }
  }

  public static function serveBootStrap5($type = 'css') {

    if ('css' == $type) {
      $lib = __DIR__ . '/resource/bootstrap5/bootstrap.min.css';
      if ('blue' == \currentUser::option('theme')) {
        $lib = __DIR__ . '/resource/bootstrap5/bootstrap-blue.min.css';
      } elseif ('orange' == \currentUser::option('theme')) {
        $lib = __DIR__ . '/resource/bootstrap5/bootstrap-orange.min.css';
      } elseif ('pink' == \currentUser::option('theme')) {
        $lib = __DIR__ . '/resource/bootstrap5/bootstrap-pink.min.css';
      } elseif ('blue' == \config::$THEME) {
        $lib = __DIR__ . '/resource/bootstrap5/bootstrap-blue.min.css';
      } elseif ('orange' == \config::$THEME) {
        $lib = __DIR__ . '/resource/bootstrap5/bootstrap-orange.min.css';
      } elseif ('pink' == \config::$THEME) {
        $lib = __DIR__ . '/resource/bootstrap5/bootstrap-pink.min.css';
      }
      \sys::logger( sprintf('<%s> %s', $lib, __METHOD__));
      self::serve($lib);
    } elseif ('polyfill' == $type) {
      $lib = sprintf('%s/resource/bootstrap4-5.polyfill.css', __DIR__);
      // \sys::logger( sprintf('<%s> %s', $lib, __METHOD__));
      self::serve($lib);
    } elseif ('js' == $type) {
      $lib = sprintf('%s/resource/bootstrap5/js/bootstrap.bundle.min.js', __DIR__);
      // \sys::logger( sprintf('<%s> %s', $lib, __METHOD__));
      self::serve($lib);
    }
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
          'libFile' => \config::tempdir()  . '_fullcalendar4_tmp.css'

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
          'libFile' => \config::tempdir()  . '_fullcalendar4_tmp.js'

        ]);
      }
    } else {
      throw new \Exception('Cannot locate fullcalendar-4 bootstrap');
    }
  }

  public static function set_error_handler() {
    errsys::initiate(false);
  }

  public static function text2html($inText, $maxrows = -1, $allAsteriskAsList = false) {
    /**
     * text2html: converts plain text to html by swaping in <br /> for \n
     *
     * $inText : text to be converted
     * $maxRows : the number of rows to convert - default -1 == all
     * $allAsteriskAsList : convert * (asterisk) to list (<ul><li>{text}</li></ul>)
     **/

    if ($maxrows > 0) {
      $a = [
        "/\\\\n/",
        "/(\n)|(\\\\n)/"
      ];
      $x = preg_split("/\n/", $inText);
      while (count($x) > ($maxrows + 1))
        array_pop($x);
      $inText = implode("<br />", $x);
    }

    $a = [
      "/\r\n/",
      "/---\\n/",
      "/\\\\n/",
      "/\n/",
      "/$\*/"
    ];

    $aR = [
      "\n",
      '<hr align="left" style="width: 200px; margin: 0;" />',
      '<br />',
      '<br />',
      '<br />&bull;'
    ];

    if ($allAsteriskAsList) {
      $a[] = "/\*/";
      $aR[] = "<br />&bull;";
      $inText = rtrim($inText, " .*");
    }

    return (preg_replace($a, $aR, $inText));
  }

  public static function trace($v, $level = 0) {
    self::logger($v);
    $level = (int)$level;
    $iLevel = 0;
    foreach (debug_backtrace() as $e) {
      if (isset($e['file'])) {
        self::logger(sprintf('%s(%s)', $e['file'], $e['line']));
      } else {
        self::logger(print_r($e, true));
      }

      if ($level > 0 && ++$iLevel > $level) {
        break;
      }
    }
  }

  public static function traceCaller() {
    $trace = debug_backtrace();
    if (isset($trace[2])) {
      $caller = $trace[2];
      if (isset($caller['class'])) {
        return sprintf('%s/%s', $caller['class'], $caller['function']);
      }

      return $caller['function'];
    }

    return 'unknown caller';
  }
}
