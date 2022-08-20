<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc\core;

use sys;

class user {
  public $id = 0;

  public $name = '';

  public $email = '';

  public function __construct() {
  }

  public function valid() {
    /**
     * if this function returns true you are logged in
     */

    return (true);
  }

  public function isadmin() {
    return ($this->valid());
  }

  public function sync(\dvc\oauth $oauth) {
    sys::logger('_user::sync => placeholder function - you probably want to write your own');
  }

  public static function has_uid() {
    return (isset($_COOKIE['_bwui']));
  }

  public static function uid() {
    if (!(isset($_COOKIE['_bwui'])))
      $uc = md5(sprintf('%s:%s', \userAgent::os(), (string)time()));

    else
      $uc = $_COOKIE['_bwui'];

    $dao = new \dao\bwui;
    $dao->getByUID($uc);

    if ((float)phpversion() < 7.3) {
      setcookie(
        '_bwui',
        $uc,
        $expires = time() + (60 * 60 * 24 * \config::$COOKIE_AUTHENTICATION_EXPIRES_DAYS),
        $path = '/; samesite=strict',
        $domain = '',
        $secure = true
      );
    } else {
      setcookie('_bwui', $uc, [
        'expires' => time() + (60 * 60 * 24 * \config::$COOKIE_AUTHENTICATION_EXPIRES_DAYS),
        'path' => '/',
        'domain' => '',
        'secure' => !(Request::get()->ServerIsLocal() || Request::get()->ClientIsLocal()),
        'httponly' => false,
        'samesite' => 'strict'

      ]);
    }

    //~ $u = sprintf( '%s:%s', userAgent::os(), $uc);
    return ($uc);
  }

  public static function hasGoogleFlag() {
    if ($uid = self::uid()) {
      $uDao = new \dao\bwui;
      $uDto = $uDao->getByUID($uid);
      return ($uDto->bygoogle);
    }
  }

  public static function setGoogleFlag($v = 1) {
    if ($uid = self::uid()) {
      $uDao = new \dao\bwui;
      if ($uDto = $uDao->getByUID($uid))
        $uDao->UpdateByID([
          'updated' => \db::dbTimeStamp(),
          'bygoogle' => (int)$v

        ], $uDto->id);
    }
  }

  public static function setUserName($v = '') {
    if ($uid = self::uid()) {
      $uDao = new \dao\bwui;
      if ($uDto = $uDao->getByUID($uid))
        $uDao->UpdateByID([
          'updated' => \db::dbTimeStamp(),
          'username' => $v

        ], $uDto->id);
    }
  }

  public static function clearGoogleFlag() {
    self::setGoogleFlag(0);
  }
}
