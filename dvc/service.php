<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * This work is licensed under a Creative Commons Attribution 4.0 International Public License.
 *      http://creativecommons.org/licenses/by/4.0/
 *
 * DO NOT change this file
 * Copy it to <application>/app/dvc/ and modify it there
*/

namespace dvc;

class service extends core\application {
	protected $service = true;

	public function __construct( $rootPath) {
		parent::__construct( $rootPath);

	}

}
