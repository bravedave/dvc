<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/

namespace dvc\mssql;

class dbResult {
	protected $result = FALSE;
	protected $db;

	public function __construct(  $result = NULL, $db = NULL) {
		if ( $result)
			$this->result = $result;

		if ( $db)
			$this->db = $db;

	}

	public function __destruct() {
		if ( $this->result) {
			sqlsrv_free_stmt( $this->result);
			//~ \sys::logger( 'free statement');

		}

	}

	public function fetch() {
		return ( sqlsrv_fetch_array( $this->result, SQLSRV_FETCH_ASSOC));

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
	public function dtoSet( $func = NULL, $template = NULL) {
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