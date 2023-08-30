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
use bravedave\dvc\{cssmin, dbi, errsys, logger, Response, hitter};

abstract class sys extends bravedave\dvc\sys {
  protected static $_loglevel = 1;

  static $debug = false;

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

          Response::serve($path);
        } else {

          $path = sprintf(
            '%s%stemplates%s%s',
            __DIR__,
            DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR,
            $template
          );

          if (file_exists($path)) Response::serve($path);
        }
      }
    }
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
      // error_log($v);
      // error_log($v);
      logger::info($v);
    }
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

  public static function logSQL($v, $level = 0) {

    logger::sql($v);
  }

  public static function serve($path): void {

    Response::serve($path);
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

    Response::serveBootStrap($type, $fontFile);
  }

  public static function serveBootStrap5($type = 'css'): void {

    Response::serveBootStrap5($type);
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
