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
use dvc;

class bwui extends _dao {
	protected $_db_name = 'bwui';

	protected static $_db_allways_check_bwui = true;

	protected function structure( $name = null ) {
		if ( is_null( $name))
			$name = $this->db_name();

		//~ \sys::logger( 'bwui check');
		if ( self::$_db_allways_check_bwui) {
			self::$_db_allways_check_bwui = false;
			$this->_db_allways_check_structure = false;

			if ( 'sqlite' == \config::$DB_TYPE) {
				$dbc = new dvc\sqlite\dbCheck( $this->db, 'bwui' );

			}
			else {
				$dbc = new dbCheck( $this->db, 'bwui' );

			}

			$dbc->defineField( 'created', 'datetime');
			$dbc->defineField( 'updated', 'datetime');
			$dbc->defineField( 'key', 'varchar', 32);
			$dbc->defineField( 'username', 'varchar', 32);
			$dbc->defineField( 'bygoogle', 'tinyint');
			$dbc->defineField( 'creds', 'blob');
			//~ \sys::logger( 'bwui checked');

			return ( $dbc );

		}

		return ( false);

	}

	public function getByUID( $uid, $fields = '*') {
		if ( \strings::isValidMd5( $uid)) {
			$key = $this->escape( $uid);
			if ( $key == $uid) {
				if ( $res = $this->Result( sprintf( 'SELECT %s FROM %s WHERE `key` = "%s"', $fields, $this->_db_name, $uid))) {
					if ( $dto = $res->dto())
						return $dto;

				}

				$ts = self::dbTimeStamp();
				if ( $id = $this->Insert( ['created' => $ts,'updated' => $ts,'key' => $uid]))
					return ( $this->getByID( $id));

			}
			else {
				throw new Exceptions\SecurityViolation;

			}

		}
		else {
			throw new Exceptions\SecurityViolationMD5;

		}

		return ( false);

	}

}