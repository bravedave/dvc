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

  static $crons = [
    crond\FibreModule1::class,
    crond\FibreModule2::class
  ];


  static function checkdatabase() {
    $dao = new dao\dbinfo(null, self::dataPath());
    // $dao->debug = true;
    $dao->checkVersion('db', self::db_version);
  }

  static function serviceWorker() {
    return implode(DIRECTORY_SEPARATOR, [
      __DIR__,
      'js',
      'service-worker.js'
    ]);
  }

  static function webWorker() {
    return implode(DIRECTORY_SEPARATOR, [
      __DIR__,
      'js',
      'worker.js'
    ]);
  }
}
