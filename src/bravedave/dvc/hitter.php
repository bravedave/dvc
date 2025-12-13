<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\dvc;

class hitter {
	protected $count = 0;
	protected $name = 'hitter';

	function __construct($name = NULL) {
		if ($name) $this->name = $name;
	}

	function __destruct() {

		logger::info(sprintf('<%s : %d> %s', $this->name, $this->count, logger::caller()));
	}

	function hit() {
		$this->count++;
	}

	function hits($i) {
		$this->count = $i;
	}
}
