<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc;

class hitter {
	protected $count = 0;
	protected $name = 'hitter';

	function __construct( $name = NULL ) {
		if ( $name)
			$this->name = $name;

	}

	function __destruct() {
		\sys::logger( sprintf( '%s : %d', $this->name, $this->count ));

	}

	function hit() {
		$this->count ++;

	}

	function hits( $i) {
		$this->count = $i;

	}

}
