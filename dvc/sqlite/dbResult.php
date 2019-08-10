<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/

namespace dvc\sqlite;

class dbResult {
	protected $result = false;
	protected $db;

	public function __construct(  $result = null, $db = null) {
		if ( $result)
			$this->result = $result;

		if ( $db)
			$this->db = $db;

	}

	public function __destruct() {}

	public function fetch() {
		return ( $this->result->fetchArray( SQLITE3_ASSOC));

	}

	public function dto( $template = NULL) {
		if ( $dto = $this->fetch()) {
			if ( is_null( $template))
				return ( new \dao\dto\dto( $dto));

			return ( new $template( $dto));

		}

		return ( FALSE);

	}

	/**
	 *	extend like:
	 *		$dtoSet = $res->dtoSet( function( $dto) {
	 *			return $dto;
	 *
	 *		});
	 */
	public function dtoSet( $func = null, $template = null) {
		$ret = [];
		if ( is_callable( $func)) {
			while ( $dto = $this->dto( $template)) {
				if ( $d = $func( $dto))
					$ret[] = $d;

			}

		}
		else {
			while ( $dto = $this->dto( $template))
				$ret[] = $dto;

		}

		return ( $ret);

	}

}
