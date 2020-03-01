<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 *  DO NOT change this file
 *  Copy it to <application>/app/dvc/ and modify it there
*/

class application extends dvc\application {
	static function run( $dir = null ) {
		if ( is_null( $dir ))
			throw new Exception( 'you must provide a path' );

		$app = new application( $dir );

	}

}
