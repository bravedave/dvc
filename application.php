<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	DO NOT change this file
	Copy it to <application>/app and modify it there
	*/
class application extends dvc\application {
	static function run( $dir = NULL ) {
		if ( is_null( $dir ))
			throw new Exception( 'you must provide a path' );

		$app = new application( $dir );

	}

}
