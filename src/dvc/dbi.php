<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/

namespace dvc;

class dbi extends db {
	protected static $dbiCount = 0;
	protected $_valid = false;

	function valid() {
		return ( $this->_valid);

	}

	static function getDBI() {
		return ( \sys::dbi());

	}

	static function SQL( $sql ) {
		return ( \sys::dbi()->Q( $sql ));

	}

	static function StaticInsert( $table, $a ) {
		$db = \sys::dbi();
		return ( $db->Insert( $table, $a ));

	}

	static function StaticUpdate( $table, $a, $scope ) {
		$db = \sys::dbi();
		return ( $db->Update( $table, $a, $scope ));

	}

	public function __construct() {
		if ( config::$DB_TYPE == 'none' || config::$DB_TYPE == 'disabled' )
			return;

		self::$dbiCount ++;
		if ( self::$dbiCount  > 1)
			sys::logger( sprintf( 'db initialized (%s)', self::$dbiCount ), 3 );

		//~ sys::logger( sprintf( 'db initialized (%s,%s,%s,%s)',
			//~ config::$DB_HOST, config::$DB_NAME, config::$DB_USER, config::$DB_PASS));

		parent::__construct( config::$DB_HOST, config::$DB_NAME, config::$DB_USER, config::$DB_PASS);

		$this->_valid = TRUE;

	}

	public function result(  $query ) {
		try {
			$dbResult = new dbResult( $this->Q( $query), $this);
			return ( $dbResult );

		}
		catch( \Exception $e) {
			throw new Exceptions\SQLException;

		}

	}

}