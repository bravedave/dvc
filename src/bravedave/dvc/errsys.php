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

abstract class errsys {
  static protected $_shutup = false;
  static protected $_currentUser = false;

  static protected function _email_support($mailMessage) {

    if (config::$EMAIL_ERRORS_TO_SUPPORT) {

      $header = array(
        sprintf('From: %s <%s>', config::$WEBNAME, config::$WEBEMAIL),
        sprintf('Reply-To: %s <%s>', config::$WEBNAME, config::$SUPPORT_EMAIL),
        sprintf('Return-Path: %s <%s>', config::$WEBNAME, config::$SUPPORT_EMAIL),
        'Content-Type: text/plain',
        sprintf('Date: %s', date(DATE_RFC2822))
      );

      // These two to help avoid spam
      $host = (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : getenv('HOSTNAME'));

      $header[] = sprintf('Message-ID: <%s>', date('YmdHis') . 'TheSystem@' . $host);
      $header[] = sprintf('X-Mailer: PHP v%s', phpversion());

      $headers = implode("\r\n", $header);
      // $scriptname = strtolower( $_SERVER[ "SCRIPT_NAME" ]);

      try {

        $mail = \sys::mailer();
        $mail->IsHTML(false);
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        $mail->Subject  = config::$WEBNAME . " PHP Error";
        $mail->AddAddress(config::$SUPPORT_EMAIL, config::$SUPPORT_NAME);

        $mail->Body = $mailMessage;
        if ($mail->send()) {

          logger::info(sprintf('<error - send email> %s', __METHOD__));
        } else {

          logger::info(sprintf(
            '<error - send email failed - fallback to mail : %s> %s',
            $mail->ErrorInfo,
            __METHOD__
          ));

          mail(config::$SUPPORT_EMAIL, config::$WEBNAME . " PHP Error", $mailMessage, $headers, "-f" . config::$SUPPORT_EMAIL);
        }
      } catch (\Exception $e) {

        mail(config::$SUPPORT_EMAIL, config::$WEBNAME . " PHP Error", $mailMessage, $headers, "-f" . config::$SUPPORT_EMAIL);
      } catch (\Exception $e) {

        print '<h1>Could not send error report</h1>';
        print $mailMessage;
      }
    } else {

      error_log($mailMessage);
    }
  }

  static protected function _msg($e) {

    if (method_exists($e, 'format')) {
      return $e->format() . ' format';
    } else {
      $msg = [
        sprintf("%s(%s)", $e->getMessage(), $e->getCode()),
        sprintf("%s(%s)", $e->getFile(), $e->getLine()),
        sprintf("%s", $e->getTraceAsString()),
        'compiled'
      ];

      if (isset($_SERVER['REQUEST_URI'])) $msg[] = sprintf("Request URI: %s\n", $_SERVER['REQUEST_URI']);
      if (isset($_SERVER['HTTP_REFERER'])) $msg[] = sprintf("Referer: %s\n", $_SERVER['HTTP_REFERER']);
      if (isset($_SERVER['REMOTE_ADDR'])) $msg[] = sprintf("Remote Address: %s\n", $_SERVER['REMOTE_ADDR']);
      if (self::$_currentUser) $msg[] = sprintf("Current User:%s\n", self::$_currentUser);

      return implode(PHP_EOL, $msg);
    }
  }

  static public function currentUser($name = null) {
    $ret = self::$_currentUser;

    if (!(is_null($name)))
      self::$_currentUser = $name;

    return ($ret);
  }

  static public function email_support($e) {
    self::_email_support(self::_msg($e));
  }

  static public function err_handler($errno, $errstr, $errfile, $errline) {
    if (self::$_shutup)
      return;

    $l = error_reporting();
    if ($l & $errno) {
      $exit = false;
      $type = 'Unknown Error';

      switch ($errno) {
        case E_USER_ERROR:
          $type = 'Fatal Error';
          $exit = true;
          break;

        case E_USER_WARNING:
          $type = 'User Warning';
          break;

        case E_WARNING:
          $type = 'Warning';
          break;

        case E_USER_NOTICE:
          $type = 'User Notice';
          break;

        case E_NOTICE:
          $type = 'Notice';
          break;

        case @E_STRICT:
          $type = 'Strict Notice';
          break;

        case @E_RECOVERABLE_ERROR:
          $type = 'Catchable';
          break;

        default:
          $exit = true;
          break;
      }


      if ($exit) {
        if (!self::$_shutup) {
          $exception = new \Exception();
          self::exc_handler($exception);

          $message = sprintf('%s: %s %s %s %s %s', $type, $errstr, $errno, PHP_EOL, $errfile, $errline);

          if (Request::get()->ServerIsLocal()) {

            printf('<pre>%s</pre>', $message);
            error_log($message);
          } else {

            printf("ERROR<hr /><pre>%s</pre><hr /><a href='%s'>return to home page</a>", $message, \url::$URL);
            // error_log( $message);
            self::_email_support($message);
          }
        }
        exit();
      } else {
        /*
				 * error is logged in the exception
				 */
        // if ( $errstr == 'Unknown') {
        if (preg_match('/^Unknown/', $errstr)) {
          error_log('---[probable duplicate    : error is logged in the exception]---');
          error_log(sprintf('---[controller : %s]---', \application::app()->controller()));
          error_log(sprintf('---[action : %s]---', \application::app()->action()));
          error_log(sprintf('---[method : %s]---', Request::get()->getServer('REQUEST_METHOD')));
          if (\method_exists('currentUser', 'name')) {
            error_log(sprintf('---[user : %s]---', \currentUser::name()));
          }

          \sys::trace($errno);
          error_log(sprintf('%s: %s %s %s %s', $type, $errstr, $errno, $errfile, $errline));
          error_log('---[end probable duplicate: error is logged in the exception]---');
        }

        throw new \Exception(sprintf('%s: %s %s %s %s %s', $type, $errstr, $errno, PHP_EOL, $errfile, $errline));
      }
    }

    return false;
  }

  static public function exc_handler($e) {

    if (self::$_shutup) return;

    if (Request::get()->ServerIsLocal()) {

      $msg = self::_msg($e);
      printf('<pre>%s</pre>', $msg);
      error_log($msg);
    } else {

      if (method_exists($e, 'format')) {

        $message = sprintf('%s', $e->format());
      } else {

        $message = sprintf("%s(%s)\n", $e->getMessage(), $e->getCode());
      }

      printf("ERROR<hr /><pre>%s</pre><hr /><a href='%s'>return to home page</a>", $message, \url::$URL);
      error_log($message);
      self::email_support($e);
    }
  }

  static public function initiate($log = false) {

    set_error_handler(function ($errno, $errstr, $errfile, $errline) {
      errsys::err_handler($errno, $errstr, $errfile, $errline);
    });

    set_exception_handler(function ($e) {
      errsys::exc_handler($e);
    });

    if ($log !== false) {
      if (!ini_get('log_errors'))
        ini_set('log_errors', true);
      if (!ini_get('error_log'))
        ini_set('error_log', $log);
    }
  }

  static public function shutup($state = null) {
    $ret = self::$_shutup;

    if (!(is_null($state))) self::$_shutup = $state;

    return ($ret);
  }
}
