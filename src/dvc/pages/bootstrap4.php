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

class bootstrap4 extends bootstrap {

	function __construct( $title = '' ) {
		\config::$BOOTSTRAP_VERSION = '4';
		self::$Bootstrap_Version = '4';

		parent::__construct( $title );

		$this->topOfPage = [];

	}

}
