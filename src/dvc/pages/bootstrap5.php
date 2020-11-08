<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc\pages;

class bootstrap5 extends bootstrap {

	function __construct( $title = '' ) {
		\config::$BOOTSTRAP_VERSION = '5';
		self::$Bootstrap_Version = '5';

		parent::__construct( $title );

		$this->topOfPage = [];

	}

}
