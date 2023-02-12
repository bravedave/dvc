<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

class config extends \bravedave\dvc\config {
  const db_version = 0.1;

  public static $WEBNAME = 'A Todo System';

  static function checkdatabase() {
    $dao = new dao\dbinfo(null, self::dataPath());
    // $dao->debug = true;
    $dao->checkVersion('db', self::db_version);
  }
}
