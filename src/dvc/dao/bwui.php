<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc\dao;

use config, dvc, sys;
use DateInterval;
use DateTime;

class bwui extends _dao {
  const version = 1.1;

  protected $_db_name = 'bwui';

  protected static $_db_allways_check_bwui = true;

  protected function structure($name = null) {

    if (config::$DB_CACHE == 'APC') {

      $cache = dvc\cache::instance();
      $key = $this->cacheKey(0, $this->_db_name . '_version');

      if ($version = $cache->get($key)) {

        if ($version >= self::version) return false;
      }
    }

    if (self::$_db_allways_check_bwui) {

      self::$_db_allways_check_bwui = false;
      $this->_db_allways_check_structure = false;

      $dbc = sys::dbCheck($this->_db_name);
      $dbc->defineField('created', 'datetime');
      $dbc->defineField('updated', 'datetime');
      $dbc->defineField('key', 'varchar', 32);
      $dbc->defineField('username', 'varchar', 32);
      $dbc->defineField('user_id', 'bigint');
      $dbc->defineField('bygoogle', 'tinyint');
      $dbc->defineField('creds', 'blob');

      if (\config::$DB_CACHE == 'APC') $cache->set($key, self::version);

      return $dbc;
    }

    return false;
  }

  public function garbageCollection() {

    $dt = new DateTime();
    $dt->sub(new DateInterval('P1M'));

    $sql = sprintf(
      'DELETE FROM bwui WHERE DATE( `updated`) < %s',
      $this->quote($dt->format('Y-m-d'))
    );

    $this->Q($sql);
  }

  public function getByUID($uid, $fields = '*') {

    if (\strings::isValidMd5($uid)) {

      $key = $this->escape($uid);
      if ($key == $uid) {

        $sql = sprintf(
          'SELECT %s FROM %s WHERE `key` = %s',
          $fields,
          $this->_db_name,
          $this->quote($uid)
        );

        if ($res = $this->Result($sql)) {

          if ($dto = $res->dto()) return $dto;
        }

        if ($id = $this->Insert(['key' => $uid])) return $this->getByID($id);
      } else {

        throw new Exceptions\SecurityViolation;
      }
    } else {

      throw new Exceptions\SecurityViolationMD5;
    }

    return null;
  }

  public function Insert($a) {
    $a['created'] = $a['updated'] = self::dbTimeStamp();
    return parent::Insert($a);
  }

  public function UpdateByID($a, $id) {
    $a['updated'] = self::dbTimeStamp();
    return parent::UpdateByID($a, $id);
  }
}
