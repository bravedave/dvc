<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc\sqlite;

class db {
	public $log = false;
	protected $_db = false;
	protected $_path = null;

	protected static $_instance = false;

	static function instance() {
		if ( !self::$_instance)
			self::$_instance = new self;

		return ( self::$_instance);

	}

	protected function __construct() {
		$this->_path = sprintf('%s%ssqlite.db', \config::dataPath(), DIRECTORY_SEPARATOR );
		if ( file_exists( $this->_path)) {
			$this->_db = new \SQLite3( $this->_path);	// throws exception on failure

		}
		else {
			// I prefer this naming convention because in windows you can associate the extension
			$this->_path = sprintf('%s%sdb.sqlite', \config::dataPath(), DIRECTORY_SEPARATOR );
			$this->_db = new \SQLite3( $this->_path);	// throws exception on failure

		}

		if ( $this->_db) {
			$this->_db->busyTimeout( 6000 );	// 6 seconds
			// \sys::logger( 'dvc\sqlite\db :: set timeout to 6 millisecs');

		}

	}

	public function __destruct() {
		if ( $this->_db) {
			$this->_db->close();

		}

		$this->_db = false;

	}

	public function dump() {
		if ( $tables = $this->tables()) {

			$uID = 0;
			foreach ( $tables as $table) {
				printf( '<span data-role="visibility-toggle" data-target="bqt%s">Table: %s</span><br />%s',
					$uID,
					$table,
					PHP_EOL	);
				printf( '<blockquote id=\'bqt%s\' style="font-family: monospace; display: none;">%s',
					$uID++,
					PHP_EOL	);

				/* Get field information for all columns */
				if ( $fields = $this->fieldList( $table)) {
          foreach ($fields as $field) {
            printf( '<br />%s %s %s', $field->name, $field->type, ( $field->pk ? 'primary key' : ''));

          }

				}

				print "</blockquote>\n";

			}

		}

	}

	public function escape( $value ) {
		return $this->_db->escapeString( $value );

	}

	public function flushCache() {
    if ( \config::$DB_CACHE == 'APC') {
      /**
       * the automatic caching is controlled by:
       *	=> \dao\_dao->getByID addes to cache
       *  => \dao\_dao->UpdateByID flushes the cache selectively
       *		 - and sets flushCache to FALSE - so you won't be here
       *
       *	if you are here it is because Update was called casually outside
       *	of UpdateByID <=> a master flush is required
       */
      $cache = \dvc\cache::instance();
      $cache->flush();

    }

  }

	public function field_exists( $table, $field ) {
		$ret = false;

    $fieldList = $this->fieldList( $table);
		foreach ( $fieldList as $f) {
			if ( $field === $f->name) {
        return true;
        break;  // never executes

      }

    }

		return ($ret);

	}

	public function fieldList( $table ) {
		$ret = [];
		if ( $result = $this->result( sprintf( 'PRAGMA table_info(%s)', $table))) {
			while ( $dto = $result->dto())
				$ret[] = $dto;

		}

		return ( $ret );

	}

	public function getPath() {
		return ( $this->_path);

	}

	public function Insert( $table, $a ) {
		/**
		 * Insert values into SQLite table
		 *
		 * Note: SQLite values must delimit with ' (single quote)
		 *
		 * Parameters: 	Table to update
		 * 				array of key => values
		 *
		 */
		$fA = [];
		$fV = [];
		foreach ( $a as $k => $v ) {
			$fA[] = $k;
			$fV[] = $this->escape($v);

		}

		$sql = sprintf( "INSERT INTO `%s`(`%s`) VALUES('%s')", $table, implode( "`,`", $fA ), implode( "','", $fV ));

		$this->_db->exec( $sql);
		return ( $this->_db->lastInsertRowID());

	}

	public function Q( string $sql) {
		if ( $this->log) \sys::logSQL( $sql);
		if ( $result = $this->_db->query( $sql)) return ( $result);

		/****************************************
		  * You are here because there was an error **/
		$message = sprintf( "Error : SQLite : %s\nError : SQLite : %s",
			$sql,
			$this->_db->lastErrorMsg );

		\sys::logSQL( $sql);
		foreach ( debug_backtrace() as $e )
			\sys::logger( sprintf( '%s(%s)', $e['file'], $e['line'] ));

		throw new \Exception( $message );

	}

  public function quote( string $val) {
		return sprintf( "'%s'", $this->escape( $val));

  }

	public function result(  $query) {
		$dbResult = new dbResult( $this->Q( $query), $this);
		return ( $dbResult);

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

	public function table_exists( string $table) : bool {
    $sql = sprintf(
      "SELECT name FROM sqlite_master WHERE type='table' and name='%s'",
      $this->escape( $table)

    );

		if ( $result = $this->result( $sql)) {
			if ( $dto = $result->dto()) {
				return true;

			}

		}

		return ( false );

	}

	public function Update( $table, $a, $scope, $flushCache = TRUE ) {
    if ( (bool)$flushCache) $this->flushCache();

		/**
		 * Update values into SQLite table
		 *
		 * Note: SQLite values must delimit with ' (single quote)
		 *
		 * Parameters: 	Table to update
		 * 				array of key => values
		 * 				scope of update : e.g. 'WHERE id = 1'
		 */
		$aX = [];
		foreach ( $a as $k => $v )
			$aX[] = "`$k` = '" . $this->escape($v) . "'";

		$sql = sprintf( 'UPDATE `%s` SET %s %s', $table, implode( ', ', $aX ), $scope);
		return ( $this->Q($sql));

	}

	public function valid() {
		if ( !self::$_instance)
			self::$_instance = new self;

		if ( self::$_instance)
			return ( true);

		return ( false);

	}

	public function zip() {
		$debug = false;
		// $debug = TRUE;

		$zip = new \ZipArchive();
		$filename = sprintf( '%s%sdb.zip', \config::dataPath(), DIRECTORY_SEPARATOR);

		if ( file_exists( $filename)) {
			unlink( $filename);

		}

		if ( $debug) \sys::logger( sprintf( 'sqlite\db->zip() : <%s>', $filename));

		if ( $zip->open($filename, \ZipArchive::CREATE) !==TRUE ) {
			\sys::logger( sprintf( 'sqlite\db->zip() : cannot open <%s>', $filename));

		}
		else {
			// $this->_db->close();
			// $this->_db = NULL;

			if ( $debug) \sys::logger( sprintf( 'sqlite\db->zip() : adding <%s>', $this->_path));
			$zip->addFile( $this->_path, 'db.sqlite');

			if ( $debug) \sys::logger( sprintf( 'sqlite\db->zip() : numfiles : %s', $zip->numFiles));
			if ( $debug) \sys::logger( sprintf( 'sqlite\db->zip() : status : %s', $zip->status));

			$zip->close();

			return ( $filename);

			// $this->_db = new \SQLite3( $this->_path);	// throws exception on failure

		}

	}

}
