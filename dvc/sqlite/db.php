<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/

Namespace dvc\sqlite;

class db extends \SQLite3 {
	public $log = FALSE;

	function __construct() {
		$root = sprintf('%s', \application::app()->getRootPath());
		$path = sprintf('%s%sdata', \application::app()->getRootPath(), DIRECTORY_SEPARATOR );

		if ( is_writable( $root ) || is_writable( $path)) {
			if ( !is_dir( $path))
				mkdir( $path, '0777');

			if ( !is_dir( $path))
				throw new \Exception( 'error/nodatapath');

			$path = sprintf('%s%ssqlite.db', $path, DIRECTORY_SEPARATOR );
			$this->open( $path);
			return;

		}
		printf( 'please create a writable data folder : %s', $path );
		printf( '<br /><br />mkdir --mode=0777 %s', $path );

		throw new \Exception( 'unable to open database');

	}

	function __destruct() {
		$this->close();

	}

	public function escape( $value ) {
		return $this->escapeString( $value );

	}

	public function Insert( $table, $a ) {
		$fA = array();
		$fV = array();
		foreach ( $a as $k => $v ) {
			$fA[] = $k;
			$fV[] = $this->escape($v);

		}

		$sql = sprintf( 'INSERT INTO `%s`(`%s`) VALUES("%s")', $table, implode( "`,`", $fA ), implode( '","', $fV ));

		$this->exec( $sql);
		return ( $this->lastInsertRowID());

	}

	public function Update( $table, $a, $scope ) {
		$aX = array();
		foreach ( $a as $k => $v )
			$aX[] = "`$k` = '" . $this->escape($v) . "'";

		$sql = sprintf( 'UPDATE `%s` SET %s %s', $table, implode( ', ', $aX ), $scope);
		return ( $this->Q($sql));

	}

	public function Q( string $sql) {
		if ( $this->log) \sys::logSQL( $sql);
		if ( $result = $this->query( $sql)) return ( $result);

		/****************************************
		  * You are here because there was an error **/
		$message = sprintf( "Error : SQLite : %s\nError : SQLite : %s",
			$query,
			$this->lastErrorMsg );

		\sys::logSQL( $sql);
		foreach ( debug_backtrace() as $e )
			\sys::logger( sprintf( '%s(%s)', $e['file'], $e['line'] ));

		throw new \Exception( $message );

	}

	public function result(  $query) {
		$dbResult = new dbResult( $this->Q( $query), $this);
		return ( $dbResult);

	}

}

