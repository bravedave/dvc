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

use config;

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

  public static function isWindows() {

    return ('WIN' === strtoupper(substr(PHP_OS, 0, 3)));
  }

  public static function mailer() {
    $mail = new \PHPMailer;
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
}
