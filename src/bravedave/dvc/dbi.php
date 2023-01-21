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

class dbi extends db {
  protected static $dbiCount = 0;
  protected $_valid = false;

  function valid() {

    return ($this->_valid);
  }

  static function getDBI() {

    return (\sys::dbi());
  }

  static function SQL($sql) {

    return (\sys::dbi()->Q($sql));
  }

  public function __construct() {

    if (config::$DB_TYPE == 'none' || config::$DB_TYPE == 'disabled') return;

    self::$dbiCount++;
    if (self::$dbiCount  > 1) logger::info(sprintf('db initialized (%s)', self::$dbiCount), 3);

    //~ sys::logger( sprintf( 'db initialized (%s,%s,%s,%s)',
    //~ config::$DB_HOST, config::$DB_NAME, config::$DB_USER, config::$DB_PASS));

    parent::__construct(config::$DB_HOST, config::$DB_NAME, config::$DB_USER, config::$DB_PASS);

    $this->_valid = TRUE;
  }
}
