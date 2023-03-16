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

  public static function debug(array|string $msg): void {

    if (config::$LOG_DEBUG) self::info($msg, self::prefix_debug);
  }

  public static function deprecated(array|string $msg): void {

    if (!config::$LOG_DEPRECATED) return;

    $_trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
    $trace = array_values(array_filter($_trace, function ($c) {
      $exclude = [
        'dvc\dao\_dao',
        'bravedave\dvc\{closure}',
        'bravedave\dvc\dbResult',
        'bravedave\dvc\logger',
        'Composer\Autoload\ClassLoader',
        'MatthiasMullie\Scrapbook\Adapters\Apc'
      ];

      $excludeFunctions = [
        'bravedave\dvc\{closure}',
        'bravedave\\dvc\\{closure}',
        'include_once'
      ];

      if ($c['class'] ?? null) {

        if (in_array($c['class'], $exclude)) return false;

        return true;
      }

      if ($c['function'] ?? null) {

        if (in_array($c['function'], $excludeFunctions)) return false;

        return true;
      }

      return false;
    }));

    if ($caller = $trace[0] ?? false) {

      self::info(sprintf('<%s> %s::%s', $msg, $caller['class'] ?? '', $caller['function']));
    } else {

      self::info(sprintf('<%s> %s::%s', $msg, __METHOD__));
    }
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
