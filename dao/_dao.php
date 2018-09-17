<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/
NameSpace dao;

abstract class _dao {
	public $log = FALSE;
	public $db;
	protected $_db_name = NULL;
	protected $_db_allways_check_structure = TRUE;
	protected $template = NULL;

	protected $_sql_getByID = 'SELECT * FROM %s WHERE id = %d';
	protected $_sql_getAll = 'SELECT %s FROM %s %s';

	public static function dbTimeStamp() {
		return ( \db::dbTimeStamp());

	}

	function __construct( \dvc\dbi $db = NULL ) {
		if ( is_null( $db ))
			$this->db = \sys::dbi();

		else
			$this->db = $db;

		$this->TableChecks();
		$this->before();

	}

	/*
	 * Abstract method placeholder for use by
	 * the child class. This method is called
	 * at the end of __construct()
	 *
	 * avoid replacing the default __construct
	 * method use before instead
	 */
	protected function before() {
		/**
		 * Inspired by something I read in the fuelPHP documentation
		 * this method is called at the end of __construct and can
		 * be used to modify the _controller class
		 */

	}

	public function db_name() {
		return ( $this->_db_name );

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

	public function Update( $a, $condition, $flushCache = TRUE) {
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
			$key = sprintf( '%s.%s', $this->db_name(), $id);
			$cache->delete( $key);

		}

		return ( $this->Update( $a, sprintf( 'WHERE id = %d', $id), $flushCache = FALSE));

	}

	public function Result( $query ) {
		$this->db->log = $this->log;
		return ( $this->db->Result( $query ));

	}

	public function Q( $query ) {
		$this->db->log = $this->log;
		return ( $this->db->Q( $query ));

	}

	public function escape( $s ) {
		return ( $this->db->escape($s));

	}

	public static function asDTO( \dvc\dbResult $res, $template = NULL) {
		$r = [];
		while ( $dto = $res->dto( $template)) {
			$r[] = $dto;

		}

		return ( $r);

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

	protected function TableExists( $table = NULL) {
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

	protected function check() {
		if ( $dbc = $this->structure())
			$dbc->check();

	}

	protected function structure( $name = NULL) {
		return ( FALSE );

	}

	public function getAll( $fields = '*', $order = '') {
		if ( is_null( $this->_db_name)) {
			throw new Exceptions\DBNameIsNull;

		}

		$this->db->log = $this->log;
		return ( $this->Result( sprintf( $this->_sql_getAll, $fields, $this->db_name(), $order )));

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

	public function delete( $id) {
		if ( is_null( $this->_db_name)) {
			throw new Exceptions\DBNameIsNull;

		}

		$this->db->log = $this->log;
		$this->Q( sprintf( 'DELETE FROM %s WHERE id = %d', $this->_db_name, (int)$id ));

	}

}
