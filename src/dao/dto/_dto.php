<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dao\dto;

class _dto {
  public $__cache_version = '';
  public $__cache_suffix = '';

	public function __construct( $row = null ) {
    $this->__cache_version = \config::$DB_CACHE_VERSION;
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
