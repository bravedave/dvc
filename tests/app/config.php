<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

class config extends bravedave\dvc\config {
  const db_version = 0.1;

  const label_todo = 'A Todo System';

  static $BOOTSTRAP_VERSION = '5';
  static $PAGE_LAYOUT = 'left';

  // static $PAGE_TEMPLATE = '\dvc\pages\bootstrap5';

  static $LOG_DEPRECATED = true;

  static function checkdatabase() {
    $dao = new dao\dbinfo(null, self::dataPath());
    // $dao->debug = true;
    $dao->checkVersion('db', self::db_version);
  }
}
