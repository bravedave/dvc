<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/

Namespace dvc\sqlite;

class dbResult {
	protected $result = FALSE;
	protected $db;

	public function __construct(  $result = NULL, $db = NULL) {
		if ( $result)
			$this->result = $result;

		if ( $db)
			$this->db = $db;

	}

	public function __destruct() {}

	public function fetch() {
		return ( $this->result->fetchArray( SQLITE3_ASSOC));

	}

	public function dto() {
		if ( $o = $this->fetch())
			return ( (object)$o);

		return ( FALSE);

	}

}
