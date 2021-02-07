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

use sys;

class sitemap extends _dao {
	protected $_db_name = 'sitemap';

	protected static $_db_allways_check_sitemap = true;

	protected function structure( $name = null) {
		if ( is_null( $name))
			$name = $this->db_name();

		if ( self::$_db_allways_check_sitemap) {
			self::$_db_allways_check_sitemap = false;

			$dbc = sys::dbCheck($this->_db_name);
			$dbc->defineField( 'path', 'text' );
			$dbc->defineField( 'visits', 'bigint', 20 );
			$dbc->defineField( 'exclude_from_sitemap', 'tinyint' );

			return ( $dbc );

		}

		return ( false);

	}

	public function getbyPath( $path) {
    $sql = sprintf(
      'SELECT * FROM sitemap WHERE path = "%s" LIMIT 1',
      $path

    );

		if ( $res = $this->Result( $sql)) {
      return $res->dto();

		}

		return ( false);

	}

	public function getAll( $fields = '*', $order = '' ) {
		return ( self::asDTO( parent::getAll()));

	}

	public function getSiteMap() {
		if ( $this->db->valid())
			return ( self::asDTO( $this->db->Result( 'SELECT * FROM sitemap WHERE exclude_from_sitemap = 0')));

		return ( false);

	}

}
