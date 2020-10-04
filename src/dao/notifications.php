<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dao;

class notifications extends _dao {
  const _version = 1;
  protected static $dbVersion = 0;
  protected $_db_name = 'notifications';

	protected function structure( $name = null) {
		if ( is_null( $name)) $name = $this->db_name();

    self::$dbVersion = (int)\config::option( 'notifications_db_version');
    if ( self::$dbVersion < self::_version) {
      \config::option( 'notifications_db_version', self::_version);

      $dbc = \sys::dbCheck( $this->_db_name);
      $dbc->defineField( 'json', 'blob' );
      $dbc->defineField( 'endpoint', 'varchar', 500);
      $dbc->defineField( 'user_id', 'bigint');

      return ( $dbc );

    }

		return ( false);

  }

  public function deleteByEndPoint( string $endpoint) {
    $sql = sprintf(
      'DELETE FROM `%s` WHERE `endpoint` = "%s"',
      $this->db_name(),
      $this->escape( $endpoint)

    );

    \sys::logger( sprintf('<%s> %s', $sql, __METHOD__));
    $this->Q( $sql);

  }

  public function getByEndPoint( string $endpoint) {
    $sql = sprintf(
      'SELECT * FROM `%s` WHERE `endpoint` = "%s"',
      $this->db_name(),
      $this->escape( $endpoint)

    );

    if ( $res = $this->Result( $sql)) {
      return $res->dto();

    }

    return false;

  }

  public function getForUserID( int $userID) : array {
    $sql = sprintf(
      'SELECT * FROM `%s` WHERE `user_id` = "%d"',
      $this->db_name(),
      $userID

    );

    if ( $res = $this->Result( $sql)) {
      return $this->dtoSet( $res);

    }

    return [];

  }

}
