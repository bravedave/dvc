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

use dvc\dao\bwui, sys, config;

class user {

  static $checkBWUI = true;

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

  public function isadmin(): bool {

    return $this->valid();
  }

  public function option($key, $val = null) {
    /**
     * normally give some sort of granularity
     * of option to the user
     */

    return sys::option($key, $val);
  }

  public function restriction($key, $val = null) {
    /**
     * normally give some sort of granularity
     * of privilege to the user
     */

    return sys::option($key, $val);
  }

  public function sync(oauth $oauth) {

    logger::info('_user::sync => placeholder function - you probably want to write your own');
  }

  public static function getByUID() {

    $uc = $_COOKIE['_bwui'] ?? md5(sprintf('%s:%s', \userAgent::os(), (string)time()));
    return (new bwui)->getByUID($uc);
  }

  public static function has_uid(): bool {
    return isset($_COOKIE['_bwui']);
  }

  /**
   * provides a client uid for wider use
   *
   * @return string
   */
  public static function uid(): string {

    /**
     * think we are going with lax for _bwui
     */

    $bwui = self::getByUID();

    if ((float)phpversion() < 7.3) {

      setcookie(
        '_bwui',
        $bwui->key,
        $expires = time() + (60 * 60 * 24 * config::$COOKIE_AUTHENTICATION_EXPIRES_DAYS),
        $path = '/; samesite=' . config::$SAMESITE_POLICY,
        $domain = '',
        $secure = true
      );
    } else {

      setcookie('_bwui', $bwui->key, [
        'expires' => time() + (60 * 60 * 24 * config::$COOKIE_AUTHENTICATION_EXPIRES_DAYS),
        'path' => '/',
        'domain' => '',
        'secure' => 1,
        'httponly' => false,
        'samesite' => config::$SAMESITE_POLICY
      ]);
      // 'secure' => !(Request::get()->ServerIsLocal() || Request::get()->ClientIsLocal()),
    }

    //~ $u = sprintf( '%s:%s', userAgent::os(), $bwui->key);
    return $bwui->key;
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

  public static function setUser(array $a): void {

    if ($uid = self::uid()) {

      $dao = new bwui;
      if ($dto = $dao->getByUID($uid)) {

        $dao->UpdateByID($a, $dto->id);
      }
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
