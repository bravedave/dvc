<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/
NameSpace dvc;

class db {
	protected $mysqli, $dbname;

	public $log = FALSE;

	public static function dbTimeStamp() {
		return ( date( "Y-m-d H:i:s", time()));

	}

	function __construct( $host, $database, $user, $pass ) {
		$this->dbname = $database;
		$this->mysqli = @new \mysqli( $host, $user, $pass, $database );

		if ($this->mysqli->connect_error) {
			sys::logger( sprintf( '\mysqli( %s, %s, ***, %s )',	$host, $user, $database));
			sys::logger( sprintf( 'Connect Error (%s) %s', $this->mysqli->connect_errno, $this->mysqli->connect_error));
			throw new Exceptions\UnableToSelectDatabase;

		}

		$this->mysqli->set_charset( 'utf8');

	}

	function __destruct() {
		if ( $this->mysqli)
			$this->mysqli->close();

	}

	public function getCharSet() {
		return ( $this->mysqli->character_set_name());

	}

	public function getDBName() {
		return ( $this->dbname);

	}

	public function Q( $query ) {
		if ( $this->log ) sys::logSQL( $query );
		if ( $result = $this->mysqli->query($query)) return ( $result );


		/****************************************
		  * You are here because there was an error **/
		$message = sprintf( "Error : MySQLi : %s\nError : MySQLi : %s",
			$query,
			$this->mysqli->error );

		sys::logSQL( $message );
		foreach ( debug_backtrace() as $e )
			sys::logger( sprintf( '%s(%s)', $e['file'], $e['line'] ));

		throw new \Exception( $message );

	}

	public function escape( $s ) {
		return ( $this->mysqli->real_escape_string($s));

	}

	public function affected_rows() {
		return ( $this->mysqli->affected_rows);

	}

	public function Insert( $table, $a ) {
		$fA = array();
		$fV = array();
		foreach ( $a as $k => $v ) {
			$fA[] = $k;
			$fV[] = $this->mysqli->real_escape_string ($v);

		}

		$sql = sprintf( 'INSERT INTO `%s`(`%s`) VALUES("%s")', $table, implode( "`,`", $fA ), implode( '","', $fV ));

		$this->Q($sql);
		return ( $this->mysqli->insert_id);

	}

	public function Update( $table, $a, $scope, $flushCache = TRUE ) {
		if ( \config::$DB_CACHE == 'APC') {
			if ( (bool)$flushCache) {
				/*
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
				if ( \config::$DB_CACHE_DEBUG) {
					foreach ( debug_backtrace() as $e )
						sys::logger( sprintf( 'post flush: %s(%s)', $e['file'], $e['line'] ));

				}

			}

		}

		$aX = [];
		foreach ( $a as $k => $v )
			$aX[] = "`$k` = '" . $this->mysqli->real_escape_string($v) . "'";

		$sql = sprintf( 'UPDATE `%s` SET %s %s', $table, implode( ', ', $aX ), $scope);
		return ( $this->Q($sql));

	}

	public function fieldList( $table ) {
		$result = $this->Q( "SHOW COLUMNS FROM `$table`");
		$ret = array();
		while ($row = mysqli_fetch_assoc( $result ))
			$ret[] = $row["Field"];

		return ( $ret );

	}

	public function fetchFields( $table ) {
		$res = $this->Q( "SELECT * FROM `$table` LIMIT 1");
		return ( $res->fetch_fields());

	}

	public function field_exists( $table, $field ) {
		$ret = FALSE;

		$result = $this->Q( "SHOW COLUMNS FROM $table");
		if ( mysqli_num_rows($result) > 0 ) {
			while ($row = mysqli_fetch_assoc($result)) {
				if ( $row['Field'] == $field ) {
					$ret = TRUE;
					break;

				}

			}

		}
		return ($ret);

	}

	public function field_type( $v ) {
		return ( self::mysqli_field_type($v));

	}

	public static function mysqli_field_type($type_id) {
		static $types;

		if (!isset($types)) {
			$types = array();
			$constants = get_defined_constants(true);
			foreach ($constants['mysqli'] as $c => $n) if (preg_match('/^MYSQLI_TYPE_(.*)/', $c, $m)) $types[$n] = $m[1];
		}

		return array_key_exists($type_id, $types)? $types[$type_id] : "unKnown";

	}

	function dump() {
		if ( $dbR = $this->result( sprintf( 'SHOW TABLES FROM %s', \config::$DB_NAME ))) {
			$uID = 0;
			while ( $row = $dbR->fetch_row()) {
				printf( '<span data-role="visibility-toggle" data-target="bqt%s">Table: %s</span><br />%s',
					$uID,
					$row[0],
					PHP_EOL	);
				printf( '<blockquote id="bqt%s" style="font-family: monospace; display: none;">%s',
					$uID++,
					PHP_EOL	);

				/* Get field information for all columns */
				if ( $res = $this->result( sprintf( 'SELECT * FROM `%s` LIMIT 1', $this->escape( $row[0] )))) {
					$finfo = $res->fetch_fields();

					foreach ($finfo as $val)
						printf( '<br />%s %s (%s)', $val->name, $this->field_type( $val->type ), $val->length);

				}

				print "</blockquote>\n";

			}

		}
		else {
			printf( '<pre>
				DB Error, could not list tables
				MySQL Error: %s
				MySQL Host: %s
			</pre>',
				mysqli_error(),
				\config::$DB_HOST);

		}

	}

}
