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

use dvc\{
  errsys,
  Exceptions\exchangeAuthIsAStub,
  oauth,
  session
};
use strings, sys;

abstract class currentUser {
  // THE only instance of the class
  protected static $instance;

  public static function avatar() {
    return session::get('avatar', strings::url('images/avatar.png'));
  }

  public static function exchangeAuth() {
    throw new exchangeAuthIsAStub;
  }

  public static function getCalendarCredentials() {
    return null;
  }

  public static function DisplayName() {
    return self::name();
  }

  public static function id() {
    if ($u = self::user()) {
      return (int)$u->id;
    }

    return 0;
  }

  public static function name() {
    if ($u = self::user()) {
      return (int)$u->name;
    }

    return 0;
  }

  public static function isadmin() {
    return (self::user()->isadmin());
  }

  public static function option($key, $val = null) {
    return sys::option($key, $val);
  }

  public static function soAuth() {
    sys::logger('soAuth is stub');
    return (new \imap\soAccount);
  }

  public static function sync(oauth $o) {
    if (method_exists(self::user(), 'sync'))
      return (self::user()->sync($o));

    sys::logger('user class does not correctly inherit _user (legacy did not require this, but (e.g.) use of oauth does)');
    return (false);
  }

  public static function user() {
    if (!isset(self::$instance)) {
      self::$instance = new \user;
      // sys::logger( 'currentUser::user init');
      errsys::currentUser(self::$instance->name);
    }

    return (self::$instance);
  }

  public static function valid() {
    return (self::user()->valid());
  }
}
