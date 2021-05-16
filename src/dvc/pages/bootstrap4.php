<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * DO NOT change this file
 * Copy it to <application>/app/dvc/ and modify it there
 *
*/

namespace dvc\pages;

use dvc\core\bs;

class bootstrap4 extends bootstrap {

	function __construct( $title = '' ) {
		bs::$VERSION = \config::$BOOTSTRAP_VERSION = '4';
		self::$Bootstrap_Version = '4';

		parent::__construct( $title );

		$this->topOfPage = [];

	}

}
