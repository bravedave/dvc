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

abstract class logger {

  const prefix = 'dvc.INFO';
  const prefix_debug = 'dvc.DEBUG';
  const prefix_deprecated = 'dvc.DEPRECATED';
  const prefix_sql = 'dvc.SQL';

  public static function caller(array $_ignore = []) {

    $ignore = array_merge([
      'array_walk',
      'classloader',
      'deprecated',
      'dtoset',
      'include',
      'include_once',
      'loadclass',
      'require',
    ], $_ignore);

    $stack = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);

    foreach ($stack as $trace) {

      if (__CLASS__ == ($trace['class'] ?? '')) continue;
      if (preg_match('@{closure}$@', $trace['function'])) continue;
      if (!in_array(strtolower($trace['function']), $ignore)) {

        return sprintf('%s::%s', $trace['class'] ?? '', $trace['function']);
      }
    }

    return '';
  }

  public static function debug(array|string $msg): void {

    if (config::$LOG_DEBUG) self::info($msg, self::prefix_debug);
  }

  public static function deprecated(array|string $msg): void {

    if (!config::$LOG_DEPRECATED) return;

    self::info(sprintf(
      '<deprecated : %s> %s',
      $msg,
      self::caller([
        '__destruct',
        '_dto',
        'bravedave\dvc\{closure}',
        'bravedave\dvc\dbresult',
        'bravedave\dvc\logger',
        'matthiasmullie\scrapbook\adapters\apc'
      ])
    ));
  }

  /**
   * dumps the variable into the log
   *
   * @param mixed $var
   * @param string $callee
   * @return void
   */
  public static function dump(mixed $var, string $callee): void {

    self::info(sprintf('<%s> %s', print_r($var, true), $callee));
  }

  public static function info(array|string $msg, string $prefix = self::prefix): void {

    if (is_array($msg)) {

      array_walk($msg, fn ($m) => error_log(sprintf('%s: %s', $prefix,  $m), 0));
    } else {

      error_log(sprintf('%s: %s', $prefix, $msg), 0);
    }
  }

  public static function sql(string $msg, string $prefix = self::prefix_sql): void {

    $sql = preg_replace(["@\r\n@", "@\n@", "@\t@", "@\s\s*@"], ' ', $msg);
    if (strlen($sql) > 950) {

      $a = str_split($sql, 950);
      foreach ($a as $s) {

        self::info($s, $prefix);
      }
    } else {

      self::info($sql, $prefix);
    }
  }
}
