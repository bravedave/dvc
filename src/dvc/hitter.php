<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	DO NOT change this file
	Copy it to <application>/app/dvc/ and modify it there
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
