<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/
namespace dao;

abstract class _dao {
	protected $_sql_getByID = 'SELECT * FROM %s WHERE id = %d';
	protected $_sql_getAll = 'SELECT %s FROM %s %s';

	public $db;
	protected $_db_name = null;
	protected $_db_allways_check_structure = true;
	public $log = false;
	protected $template = null;

	function __construct( \dvc\dbi $db = null ) {
		if ( is_null( $db ))
			$this->db = \sys::dbi();

		else
			$this->db = $db;

		$this->TableChecks();
		$this->before();

	}

	public static function asDTO( $res, $template = null) {
		$r = [];
		while ( $dto = $res->dto( $template)) {
			$r[] = $dto;

		}

		return ( $r);

	}

	protected function before() {
		/*
		* Abstract method placeholder for use by the child class.
		* This method is called at the end of __construct()
		*
		* avoid replacing the default __construct method - use before instead
		*
		* Inspired by something I read in the fuelPHP documentation
		* this method is called at the end of __construct and can
		* be used to modify the _controller class
		*/

	}

	protected function check() {
		if ( $dbc = $this->structure())
		$dbc->check();

	}

	protected function _create() {
		//~ $fields = $this->db->fieldList( $this->table);
		//~ $struct = $this->db->fetchFields( $this->db_name());
		$res = $this->Result( sprintf( 'SHOW COLUMNS FROM %s', $this->db_name()));
		$dtoSet = $res->dtoSet( function( $dto) {
			/*
				in:

				[Field] => id
				[Type] => bigint(20)
				[Null] => NO
				[Key] => PRI
				[Default] =>
				[Extra] => auto_increment

			*/

			//~ $field->Dec
			$dto->Len = 0;
			$type = strtoupper( preg_replace( '@\(.*$@', '', $dto->Type));

			if ( 'BIGINT' == $type || 'SMALLINT' == $type || 'TINYINT' == $type || 'INT' == $type) {
				$dto->Len = trim( preg_replace( '@^.*\(@', '', $dto->Type),') ');
				$dto->Type = $type;
				$dto->Default = (int)$dto->Default;

			}
			elseif ( 'DATE' == $type || 'DATETIME' == $type) {
				$dto->Type = $type;

			}
			elseif ( 'MEDIUMTEXT' == $type || 'TEXT' == $type) {
				$dto->Type = $type;

			}
			elseif ( 'VARCHAR' == $type || 'VARBINARY' == $type) {
				$dto->Len = trim( preg_replace( '@^.*\(@', '', $dto->Type),') ');
				$dto->Type = $type;

			}

			return ( $dto);

		});

		$o = new dto\dto;
		foreach( $dtoSet as $dto) {
			$o->{$dto->Field} = $dto->Default;

		}

		return ( $o);

	}

	function create() {		/* returns a new dto of the file */
		if ( is_null( $this->template)) {
			return ( $this->_create());

		}

		return new $this->template;

	}

	public function db_name() {
		return ( $this->_db_name );

	}

	public static function dbTimeStamp() {
		return ( \db::dbTimeStamp());

	}

	public function delete( $id) {
		if ( is_null( $this->_db_name)) {
			throw new Exceptions\DBNameIsNull;

		}

		$this->db->log = $this->log;
		$this->Q( sprintf( 'DELETE FROM %s WHERE id = %d', $this->_db_name, (int)$id ));

	}

	public function escape( $s ) {
		return ( $this->db->escape($s));

	}

	public function getAll( $fields = '*', $order = '') {
		if ( is_null( $this->_db_name)) {
			throw new Exceptions\DBNameIsNull;

		}

		$this->db->log = $this->log;
		return ( $this->Result( sprintf( $this->_sql_getAll, $fields, $this->db_name(), $order )));

	}

	public function getFieldByID( $id, $fld) {
		if ( is_null( $this->_db_name)) {
			throw new Exceptions\DBNameIsNull;

		}

		if ( \config::$DB_CACHE == 'APC') {
			$cache = \dvc\cache::instance();
			$key = sprintf( '%s.%s.%s', $this->db_name(), $id, $fld);
			if ( $v = $cache->get( $key)) {
				return ( $v);

			}

		}

		$this->db->log = $this->log;
		if ( $res = $this->Result( sprintf( $this->_sql_getByID, $this->_db_name, (int)$id ))) {
			if ( $dto = $res->dto( $this->template)) {
				if ( \config::$DB_CACHE == 'APC') {
					$cache->set( $key, $dto->{$fld});

				}

				return ( $dto->{$fld});

			}

		}

		return ( false);

	}

	public function getByID( $id) {
		if ( is_null( $this->_db_name)) {
			throw new Exceptions\DBNameIsNull;

		}

		if ( \config::$DB_CACHE == 'APC') {
			$cache = \dvc\cache::instance();
			$key = sprintf( '%s.%s', $this->db_name(), $id);
			if ( $dto = $cache->get( $key)) {
				return ( $dto);

			}

		}

		$this->db->log = $this->log;
		if ( $res = $this->Result( sprintf( $this->_sql_getByID, $this->_db_name, (int)$id ))) {
			if ( $dto = $res->dto( $this->template)) {
				if ( \config::$DB_CACHE == 'APC') {
					$cache->set( $key, $dto);

				}

			}

			return ( $dto);

		}

		return ( FALSE);

	}

	public function Insert( $a ) {
		if ( is_null( $this->db_name())) {
			throw new Exceptions\DBNameIsNull;

		}

		$a = (array)$a;
		if ( isset( $a['id'])) {
			unset( $a['id']);

		}

		$this->db->log = $this->log;
		return ( $this->db->Insert( $this->db_name(), $a ));

	}

	public function Update( $a, $condition, $flushCache = true) {
		if ( is_null( $this->db_name()))
			throw new Exception\DBNameIsNull;

		$this->db->log = $this->log;
		return ( $this->db->Update( $this->db_name(), $a, $condition, $flushCache ));

	}

	public function UpdateByID( $a, $id ) {
		if ( is_null( $this->db_name()))
			throw new Exception\DBNameIsNull;

		if ( \config::$DB_CACHE == 'APC') {
			$cache = \dvc\cache::instance();
			$key = sprintf( '/^%s\.%s/', $this->db_name(), $id);
			$cache->delete( $key, true);

		}

		return ( $this->Update( $a, sprintf( 'WHERE id = %d', $id), $flushCache = false));

	}

	public function Result( $query ) {
		$this->db->log = $this->log;
		return ( $this->db->Result( $query));

	}

	public function Q( $query ) {
		$this->db->log = $this->log;
		return ( $this->db->Q( $query ));

	}

	protected function structure( $name = null) {
		return ( false );

	}

	protected function TableChecks() {
		if ( !$this->db->valid()) {
			return;

		}

		if ( is_null( $this->_db_name)) {
			return ( FALSE);

		}

		if ( $this->_db_allways_check_structure ) {
			$this->check();

		}
		elseif ( !( $this->TableExists())) {
			$this->check();

		}

	}

	protected function TableExists( $table = null) {
		if ( is_null( $table)) {
			$table = $this->db_name();

		}

		if ( is_null( $table)) {
			return ( FALSE);

		}

		//~ \sys::logger( "checking for: $table" );

		if ( $res = $this->Result( "SELECT
			CASE WHEN (SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'DATABASENAME' AND TABLE_NAME = '$table') < 1 then 1
			else 0
			end t")) {

			if ( $row = $res->fetch()) {
				if ( $row['t'] == 1 ) {
					return TRUE;

				}

			}

		}

		return ( FALSE);

	}

}
