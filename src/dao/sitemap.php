<?php
/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * This work is licensed under a Creative Commons Attribution 4.0 International Public License.
 *      http://creativecommons.org/licenses/by/4.0/
 *
*/

namespace dao;

class sitemap extends _dao {
	protected $_db_name = 'sitemap';

	protected static $_db_allways_check_sitemap = true;

	protected function structure( $name = null) {
		if ( is_null( $name))
			$name = $this->db_name();

		if ( self::$_db_allways_check_sitemap) {
			self::$_db_allways_check_sitemap = false;

			if ( \config::$DB_TYPE == 'sqlite' ) {
				$dbc = new \dvc\sqlite\dbCheck( $this->db, $name );

			}
			else {
				$dbc = new dbCheck( $this->db, $name );

			}

			$dbc->defineField( 'path', 'text' );
			$dbc->defineField( 'visits', 'bigint', 20 );
			$dbc->defineField( 'exclude_from_sitemap', 'tinyint' );

			return ( $dbc );

		}

		return ( false);

	}

	public function getById( $id) {
		if ( $res = $this->db->Result( sprintf( 'SELECT * FROM sitemap WHERE id = %s', (int)$id))) {
			if ( $row = $res->fetch())
				return ( new dto\dto( $row));

		}

		return ( false);

	}

	public function getDTObyPath( $path) {
		if ( $res = $this->db->Result( sprintf( 'SELECT * FROM sitemap WHERE path = "%s" LIMIT 1', $path))) {
			if ( $row = $res->fetch()) {
				return ( new dto\dto( $row));

			}

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
