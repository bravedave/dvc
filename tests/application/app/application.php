<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/


class application extends dvc\application {
	private $_settings = false;

	static function run() {
		$app = new self( __DIR__ . '/../' );

	}

}
