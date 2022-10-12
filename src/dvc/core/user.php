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

use dvc\dao\bwui, sys;

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
    return true;
  }

  public function isadmin() : bool {

    return $this->valid();
  }

  public function sync(\dvc\oauth $oauth) {

    sys::logger('_user::sync => placeholder function - you probably want to write your own');
  }

  public static function has_uid(): bool {
    return isset($_COOKIE['_bwui']);
  }

  public static function uid(): string {
    if (!(isset($_COOKIE['_bwui'])))
      $uc = md5(sprintf('%s:%s', \userAgent::os(), (string)time()));

    else
      $uc = $_COOKIE['_bwui'];

    (new bwui)->getByUID($uc);
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
    return $uc;
  }

  public static function hasGoogleFlag(): bool {

    if ($uid = self::uid()) {

      if ($uDto = (new bwui)->getByUID($uid)) {

        return $uDto->bygoogle;
      }
    }

    return false;
  }

  public static function setGoogleFlag($v = 1): void {

    if ($uid = self::uid()) {

      $dao = new bwui;
      if ($dto = $dao->getByUID($uid))
        $dao->UpdateByID([
          'bygoogle' => (int)$v
        ], $dto->id);
    }
  }

  public static function setUserID(int $id): void {

    if ($uid = self::uid()) {

      $dao = new bwui;
      if ($dto = $dao->getByUID($uid)) {

        $dao->UpdateByID([
          'user_id' => $id
        ], $dto->id);
      }
    }
  }

  public static function setUserName($v = ''): void {

    if ($uid = self::uid()) {

      $dao = new bwui;
      if ($dto = $dao->getByUID($uid)) {

        $dao->UpdateByID([
          'username' => $v
        ], $dto->id);
      }
    }
  }

  public static function clearGoogleFlag(): void {

    self::setGoogleFlag(0);
  }
}
