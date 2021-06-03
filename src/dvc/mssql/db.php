<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/

NameSpace dvc\mssql;

class db {
	public $log = FALSE;
	protected $_db;

	protected function __construct( $conn) {
		$this->_db = $conn;

	}

	static function instance( $params) {

			//~ 'CharacterSet' => 'UTF-8',
		$conn = \sqlsrv_connect( $params->serverName, $params->connectionInfo);

		if ( $conn ) {
			return new self( $conn);

		}

		//~ printf( '%s<br />', $serverName);
		//~ \sys::dump( $connectionInfo, NULL, FALSE);
		//~ \sys::dump( sqlsrv_errors(), NULL, TRUE);
		throw new \Exception( 'Connection could not be established.');

	}

	public function __destruct() {
		if ( $this->_db) {
			sqlsrv_close( $this->_db);

		}

		$this->_db = FALSE;

	}

	public function escape( $data) {
		// https://stackoverflow.com/questions/574805/how-to-escape-strings-in-sql-server-using-php
		if( is_numeric( $data)) {
			return $data;

		}

		$unpacked = unpack('H*hex', $data);
		return '0x' . $unpacked['hex'];

	}

	public function Q( $sql) {
		if ( $this->log) \sys::logSQL( $sql);
		if ( ( $result = sqlsrv_query( $this->_db, $sql)) !== FALSE) return ( $result);

		/****************************************
		  * You are here because there was an error **/
		$message = sprintf( "Error : MSSQL : %s\nError : MSSQL : %s",
			$sql,
			print_r( sqlsrv_errors(), true));

		\sys::logSQL( $sql);
		foreach ( debug_backtrace() as $e )
			\sys::logger( sprintf( '%s(%s)', $e['file'], $e['line'] ));

		throw new \Exception( $message);

	}

	public function result(  $query) {
		$dbResult = new dbResult( $this->Q( $query), $this);
		return ( $dbResult);

	}

}
