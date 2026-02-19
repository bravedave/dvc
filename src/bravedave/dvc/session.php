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

class session {
  // THE only instance of the class
  protected static $instance;
  protected $__session = [];
  protected $open = false;
  protected $domain = null;

  protected function __construct() {

    $CookieParams = session_get_cookie_params();

    if (!is_null($this->domain)) $CookieParams['domain'] = $this->domain;

    $CookieParams['secure'] = !(Request::get()->ServerIsLocal() || Request::get()->ClientIsLocal());

    if ((float)phpversion() < 7.3) {

      $CookieParams['path'] = '/; samesite=' . config::$SAMESITE_POLICY;

      session_set_cookie_params(
        $CookieParams['lifetime'],
        $CookieParams['path'],
        $CookieParams['domain'],
        $CookieParams['secure'],
        $CookieParams['httponly']
      );
    } else {

      $CookieParams['path'] = '/';
      $CookieParams['samesite'] = config::$SAMESITE_POLICY;
      $CookieParams['secure'] = 1;

      session_set_cookie_params($CookieParams);
    }

    session_cache_expire(config::$SESSION_CACHE_EXPIRE);
    session_start();
    $this->_session_regenerate();

    $this->__session = $_SESSION;

    session_write_close();
  }

  protected function __destroy() {

    if ($this->open) session_write_close();
  }

  protected function _session_regenerate(): void {

    if (!isset($_SESSION['_last_regen'])) {
      $_SESSION['_last_regen'] = time();
      session_regenerate_id(true);
    } elseif (time() - $_SESSION['_last_regen'] > 300) {
      $_SESSION['_last_regen'] = time();
      session_regenerate_id(true);
    }
  }

  protected function _edit() {

    if (!$this->open) {

      session_cache_expire(config::$SESSION_CACHE_EXPIRE);
      session_start();
      $this->_session_regenerate();
      $this->open = true;
    }
  }

  protected function _get($var, $default = '') {

    if (isset($this->__session[$var])) return $this->__session[$var];
    return $default;
  }

  protected function _close() {

    if (!isset(self::$instance)) self::$instance = new session;

    if ($this->open) {

      $this->__session = $_SESSION;  // re-read session
      $this->open = false;
      session_write_close();
    }
  }

  public static function get($var, $default = '') {

    if (!isset(self::$instance)) self::$instance = new session;

    return self::$instance->_get($var, $default);
  }

  public static function set($var, $val = null) {

    self::edit();
    if (is_null($val)) {

      if (isset($_SESSION[$var])) unset($_SESSION[$var]);
    } else {

      $_SESSION[$var] = $val;
    }
  }

  public static function edit(): void {

    if (!isset(self::$instance)) self::$instance = new session;
    self::$instance->_edit();
  }

  public static function close() : void {

    if (!isset(self::$instance)) self::$instance = new session;
    self::$instance->_close();
  }

  public static function destroy(string $msg = '') : void {

    self::close();

    session_start();
    session_destroy();

    if ($msg) logger::info(sprintf('<%s> %s', $msg, logger::caller()));
  }

  public function domain($domain = null) {

    $ret =  $this->domain;
    if (!is_null($domain)) $this->domain = $domain;
    return ($ret);
  }
}
