<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/

Namespace dvc;

class dbi extends db {
	protected static $dbi;
	protected static $dbiCount = 0;

	static function getDBI() {
		if ( !isset( self::$dbi ))
			self::$dbi = new dbi();

		return ( self::$dbi );

	}

	static function SQL( $sql ) {
		return ( self::getDBI()->Q( $sql ));

	}

	static function StaticInsert( $table, $a ) {
		$db = self::getDBI();
		return ( $db->Insert( $table, $a ));

	}

	static function StaticUpdate( $table, $a, $scope ) {
		$db = self::getDBI();
		return ( $db->Update( $table, $a, $scope ));

	}

	public function __construct() {
		if ( config::$DB_TYPE == 'none' )
			return;

		self::$dbiCount ++;
		if ( self::$dbiCount  > 1)
			sys::logger( sprintf( 'db initialized (%s)', self::$dbiCount ), 3 );

		sys::logger( sprintf( 'db initialized (%s,%s,%s,%s)',
			config::$DB_HOST, config::$DB_NAME, config::$DB_USER, config::$DB_PASS), 5 );

		parent::__construct( config::$DB_HOST, config::$DB_NAME, config::$DB_USER, config::$DB_PASS);

	}

	public function result(  $query ) {
		$dbResult = new dbResult( $this->Q( $query), $this);
		return ( $dbResult );

	}

}
