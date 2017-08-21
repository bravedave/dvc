<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/

Namespace dvc\sqlite;

class db {
	public $log = FALSE;
	protected $_db = FALSE;

	protected static $_instance = FALSE;

	static function instance() {
		if ( !self::$_instance)
			self::$_instance = new self;

		return ( self::$_instance);

	}

	private function __construct() {
		$root = sprintf('%s', \application::app()->getRootPath());
		$path = sprintf('%s%sdata', \application::app()->getRootPath(), DIRECTORY_SEPARATOR );

		if ( is_writable( $root ) || is_writable( $path)) {
			if ( !is_dir( $path))
				mkdir( $path, '0777');

			if ( !is_dir( $path))
				throw new \Exception( 'error/nodatapath');

			$path = sprintf('%s%ssqlite.db', $path, DIRECTORY_SEPARATOR );
			$this->_db = new \SQLite3( $path);
			return;

		}
		printf( 'please create a writable data folder : %s', $path );
		printf( '<br /><br />mkdir --mode=0777 %s', $path );

		throw new \Exception( 'unable to open database');

	}

	function __destruct() {
		$this->_db->close();

	}

	public function escape( $value ) {
		return $this->_db->escapeString( $value );

	}

	public function Insert( $table, $a ) {
		$fA = array();
		$fV = array();
		foreach ( $a as $k => $v ) {
			$fA[] = $k;
			$fV[] = $this->escape($v);

		}

		$sql = sprintf( 'INSERT INTO `%s`(`%s`) VALUES("%s")', $table, implode( "`,`", $fA ), implode( '","', $fV ));

		$this->_db->exec( $sql);
		return ( $this->_db->lastInsertRowID());

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
		if ( $result = $this->_db->query( $sql)) return ( $result);

		/****************************************
		  * You are here because there was an error **/
		$message = sprintf( "Error : SQLite : %s\nError : SQLite : %s",
			$query,
			$this->_db->lastErrorMsg );

		\sys::logSQL( $sql);
		foreach ( debug_backtrace() as $e )
			\sys::logger( sprintf( '%s(%s)', $e['file'], $e['line'] ));

		throw new \Exception( $message );

	}

	public function result(  $query) {
		$dbResult = new dbResult( $this->Q( $query), $this);
		return ( $dbResult);

	}

	public function dump() {
		if ( $tables = $this->tables()) {

			$uID = 0;
			foreach ( $tables as $table) {

				printf( '<span data-role="visibility-toggle" data-target="bqt%s">Table: %s</span><br />%s',
					$uID,
					$table,
					PHP_EOL	);
				printf( '<blockquote id=\'bqt%s\' style="font-family: monospace;" class="hidden">%s',
					$uID++,
					PHP_EOL	);

				/* Get field information for all columns */
				if ( $fields = $this->fieldList( $table)) {
					//~ sys::dump( $fields);
				// 	$finfo = $res->fetch_fields();

				 	foreach ($fields as $field)
				 		printf( '<br />%s %s %s', $field->name, $field->type, ( $field->pk ? 'primary key' : ''));

				}

				print "</blockquote>\n";

			}

		}

	}

	public function tables() {
		$ret = [];
		if ( $result = $this->result( "SELECT name FROM sqlite_master WHERE type='table'")) {
			while ( $dto = $result->dto()) {
				if ( !preg_match( '/^sqlite_/', $dto->name))
					$ret[] = $dto->name;

			}

		}

		return ( $ret );

	}

	public function fieldList( $table ) {
		$ret = [];
		if ( $result = $this->result( sprintf( 'PRAGMA table_info(%s)', $table))) {
			while ( $dto = $result->dto())
				$ret[] = $dto;

		}

		return ( $ret );

	}

}

