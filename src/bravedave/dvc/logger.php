<?php
/*
 * Copyright (c) 2025 David Bray
 * Licensed under the MIT License. See LICENSE file for details.
*/

namespace bravedave\dvc;

use config;

/**
 * logger - centralised application logging utility
 *
 * All output is routed through PHP's error_log(), so messages appear in the
 * web-server error log (e.g. Apache/Nginx error log, PHP-FPM log, or stderr
 * when running the built-in dev server via `vendor/bin/dvc serve`).
 *
 * Each message is prefixed with a severity tag so log files can be filtered
 * easily (e.g. `tail -f error.log | grep dvc.ERROR`).
 *
 * --- Log levels / prefixes ---
 *
 *   dvc.INFO       General informational messages (default)
 *   dvc.DEBUG      Verbose debug output (only emitted when config::$LOG_DEBUG is true)
 *   dvc.ERROR      Errors and exceptions
 *   dvc.DEPRECATED Calls to deprecated API surface (only when config::$LOG_DEPRECATED is true)
 *   dvc.SQL        SQL statements (useful for query debugging)
 *   dvc.TRACE      Full stack-trace output
 *   dvc.WARN       Non-fatal warnings
 *
 * --- Quick-start ---
 *
 *   use bravedave\dvc\logger;
 *
 *   // simple string
 *   logger::info('something happened');
 *
 *   // array - each element is logged on its own line
 *   logger::info(['line one', 'line two']);
 *
 *   // log a warning
 *   logger::warn('disk space low');
 *
 *   // log an error
 *   logger::error('could not connect to database');
 *
 *   // verbose debug (only fires when config::$LOG_DEBUG === true)
 *   logger::debug('query took 450 ms');
 *
 *   // dump a variable (uses print_r internally)
 *   logger::dump($myObject);
 *
 *   // log a SQL string (normalises whitespace, splits long queries)
 *   logger::sql($sql);
 *
 *   // log a message with a full PHP stack trace
 *   logger::trace('reached this point');
 *
 * --- Configuration (src/app/config.php or equivalent) ---
 *
 *   config::$LOG_DEBUG      = true;   // enable dvc.DEBUG messages
 *   config::$LOG_DEPRECATED = true;   // enable dvc.DEPRECATED messages
 *
 * --- Helper methods ---
 *
 *   logger::caller()       Returns a "Class::method" string identifying the
 *                          nearest non-logger frame in the call stack.
 *                          Useful for annotating custom log messages.
 *
 *   logger::traceCaller()  Returns "Class/method" of the method that called
 *                          the current method (two frames up the stack).
 */
abstract class logger {

  const prefix = 'dvc.INFO';
  const prefix_debug = 'dvc.DEBUG';
  const prefix_error = 'dvc.ERROR';
  const prefix_deprecated = 'dvc.DEPRECATED';
  const prefix_sql = 'dvc.SQL';
  const prefix_trace = 'dvc.TRACE';
  const prefix_warn = 'dvc.WARN';

  public static function caller(array $_ignore = []) {

    // note: this array is lowercase
    $ignore = array_merge([
      '__invoke',
      'array_walk',
      'classloader',
      'deprecated',
      'dtoset',
      'getdtoset',
      'include',
      'include_once',
      'loadclass',
      'protectedload',
      'load',
      '_render',
      'render',
      'require',
      dto::class . '::dto',
    ], $_ignore);

    $stack = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);

    foreach ($stack as $trace) {

      if (__CLASS__ == ($trace['class'] ?? '')) continue;
      if (preg_match('@{closure}$@', $trace['function'])) continue;
      if (!in_array(strtolower($trace['function']), $ignore)) {

        if (!in_array(strtolower(($trace['class'] ?? '') . '::' . $trace['function']), $ignore)) {

          $res = sprintf('%s::%s', $trace['class'] ?? '', $trace['function']);
          if (!in_array(strtolower($res), $ignore)) return $res;
        }
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
        'bravedave\dvc\dbresult::dto',
        'bravedave\dvc\logger',
        'dvc\pages\_page',
        'matthiasmullie\scrapbook\adapters\apc'
      ])
    ));
  }

  public static function error(array|string $msg): void {

    self::info($msg, self::prefix_error);
  }

  /**
   * dumps the variable into the log
   *
   * @param mixed $var
   * @param string $callee
   * @return void
   */
  public static function dump(mixed $var, string|null $callee = null): void {

    if (!$callee) $callee = self::caller();
    self::info(sprintf('<%s> %s', print_r($var, true), $callee));
  }

  public static function info(array|string $msg, string $prefix = self::prefix): void {

    if (is_array($msg)) {

      array_walk($msg, fn($m) => error_log(sprintf('%s: %s', $prefix,  $m), 0));
    } else {

      error_log(sprintf('%s: %s', $prefix, $msg), 0);
    }
  }

  public static function sql(string $msg, string $prefix = self::prefix_sql): void {

    $sql = preg_replace(["@\r\n@", "@\n@", "@\t@", "@\s\s*@"], ' ', $msg);
    $maxLength = 990 - strlen($prefix);
    if (strlen($sql) > $maxLength) {

      $a = str_split($sql, $maxLength);
      array_walk($a, fn($s) => self::info($s, $prefix));
    } else {

      self::info($sql, $prefix);
    }
  }

  public static function trace($v, $level = 0): void {

    self::info($v, self::prefix_trace);
    $level = (int)$level;
    $iLevel = 0;
    foreach (debug_backtrace() as $e) {

      if (isset($e['file'])) {

        self::info(sprintf('%s(%s)', $e['file'], $e['line']), self::prefix_trace);
      } else {

        self::info(print_r($e, true), self::prefix_trace);
      }

      if ($level > 0 && ++$iLevel > $level) break;
    }
  }

  public static function traceCaller(): string {

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

  public static function warn(array|string $msg): void {

    self::info($msg, self::prefix_warn);
  }
}
