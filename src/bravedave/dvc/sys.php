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

use application, config;
use PHPMailer\PHPMailer\PHPMailer;

abstract class sys {
  protected static $_dbi = null;

  public static function bootstrap_icon_dir(): string {

    return __DIR__ . '/css/bootstrap-icons/icons/';
  }

  public static function dbCheck(string $file) {

    return 'sqlite' == config::$DB_TYPE ?
      new sqlite\dbCheck(static::dbi(), $file) :
      new dbCheck(static::dbi(), $file);
  }

  public static function dbi() {

    if (is_null(static::$_dbi)) {

      if ('sqlite' == config::$DB_TYPE) {

        static::$_dbi = sqlite\db::instance();
      } else {

        static::$_dbi = new dbi;
      }
    }

    return static::$_dbi;
  }

  public static function dbCachePrefix() {

    return db::cachePrefix();
  }

  public static function diskspace() {

    $ret = (object)[
      'free' => disk_free_space(__DIR__),
      'total' => disk_total_space(__DIR__),
      'threshhold' => config::$FREE_DISKSPACE_THRESHHOLD
    ];

    $ret->exceeded = $ret->free < $ret->threshhold;
    return $ret;
  }

  public static function dump($v, $title = '', $lExit = true) {

    if (!$title) {

      $title = gettype($v) == 'object' ? get_class($v) : gettype($v);
    }

    if ($title == 'dvc\dbResult' || $title == 'dvc\sqlite\dbResult') {

      printf('<h1>%s</h1>', $title);
      while ($r = $v->dto()) {

        new html\dump($r, get_class($r));
      }
    } else {

      new html\dump($v, $title);
    }

    if ($lExit) exit;
  }

  public static function getTemplate($template) {

    if ($template) {

      if ($template = preg_replace('/[^\da-z_]/i', '', $template)) {

        $template .= '.html';

        $path = implode(DIRECTORY_SEPARATOR, [
          application::app()->getRootPath(),
          'app',
          'templates',
          $template
        ]);

        if (file_exists($path)) {

          Response::serve($path);
        } else {

          $path = implode(DIRECTORY_SEPARATOR, [
            __DIR__,
            'templates',
            $template
          ]);

          if (file_exists($path)) Response::serve($path);
        }
      }
    }
  }

  public static function isWindows() {

    return ('WIN' === strtoupper(substr(PHP_OS, 0, 3)));
  }

  public static function logger($v, $level = 0) {

    logger::info($v);
  }

  public static function logSQL($v, $level = 0) {

    logger::sql($v);
  }

  public static function mailer() {
    $mail = new PHPMailer;
    $mail->XMailer = 'BrayWorth DVC Mailer 1.0.0 (https://brayworth.com/)';

    if (static::isWindows()) {

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
      rtrim(config::dataPath(), '/ ')
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
        rtrim(config::dataPath(), '/ ')
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

    $mail->setFrom(config::$SUPPORT_EMAIL, config::$SUPPORT_NAME);
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

    if (!static::$_options) {

      if (file_exists($config = static::_options_file())) {

        static::$_options = (array)json_decode(file_get_contents($config));
      }
    }

    $ret = '';
    if (static::$_options) {

      /* return the existing value */
      if (isset(static::$_options[$key])) {

        $ret = (string)static::$_options[$key];
        if ($debug) logger::debug(sprintf('<retrieve option value : %s = %s> %s', $key, $ret,  __METHOD__));
      } elseif ($debug) {

        logger::debug(sprintf('<retrieve option value (default - not set) : %s = %s> %s', $key, $ret, __METHOD__));
      }
    } elseif ($debug) {

      logger::info(sprintf('<retrieve option value (null): %s = %s> %s', $key, $ret, __METHOD__));
    }

    if (!is_null($val)) {

      /* writer */
      if ((string)$val == '') {

        if (isset(static::$_options[$key])) unset(static::$_options[$key]);
      } else {

        static::$_options[$key] = (string)$val;
      }

      file_put_contents(
        static::_options_file(),
        json_encode(
          static::$_options,
          JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
        )
      );
    }

    return ($ret);
  }

  public static function serve($path): void {

    Response::serve($path);
  }

  public static function serveBootStrap(string $type = 'css', string $fontFile = null): void {

    Response::serveBootStrap($type, $fontFile);
  }

  public static function serveBootStrap5($type = 'css'): void {

    Response::serveBootStrap5($type);
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
      while (count($x) > ($maxrows + 1)) {
        array_pop($x);
      }
      $inText = implode("<br>", $x);
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
      '<hr align="left" style="width: 200px; margin: 0;">',
      '<br>',
      '<br>',
      '<br>&bull;'
    ];

    if ($allAsteriskAsList) {
      $a[] = "/\*/";
      $aR[] = "<br />&bull;";
      $inText = rtrim($inText, " .*");
    }

    return preg_replace($a, $aR, $inText);
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

      if ($level > 0 && ++$iLevel > $level) break;
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
