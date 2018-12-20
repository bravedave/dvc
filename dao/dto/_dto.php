<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/
namespace dao\dto;

class _dto {
	public function __construct( $row = NULL ) {
		$this->populate( $row);

	}

	protected function populate( $row = null ) {
		if ( !( is_null( $row ))) {
			foreach ( $row as $k => $v ) {
				$this->{$k} = $v;

			}

		}

	}

	public function toString() {
		$s = array();
		foreach ( $this as $k => $v) {
			$s[] = sprintf( '%s = %s', $k, $v);

		}

		return ( implode( PHP_EOL, $s));

	}

}
