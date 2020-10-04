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
  const dbVersion = 0.01;
  protected $_db_name = 'notifications';

	protected function structure( $name = null) {
		if ( is_null( $name)) {
			$name = $this->db_name();

    }

    $defaults = (object)[];
    if ( file_exists( $path = \config::defaultsPath())) {
      $defaults = (object)json_decode( file_get_contents( $path));

    }

    $v = isset( $defaults->notifications_db_version) ? $defaults->notifications_db_version : 0;
    if ( $v < self::dbVersion) {
      $defaults->notifications_db_version = self::dbVersion;
      \file_put_contents( $path, \json_encode( $defaults, JSON_PRETTY_PRINT));

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
