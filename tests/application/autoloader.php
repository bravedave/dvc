<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

if ( file_exists( $autoload = __DIR__ . '/../../autoloader.php')) {
	require_once $autoload;

}

spl_autoload_register(function ($class) {
	if ( $lib = realpath( __DIR__ . '/app/' . str_replace('\\', '/', $class) . '.php')) {
		include_once $lib;
		dvc\core\load::logger( sprintf( 'app: %s', $lib ));

		return ( true);

	}

	return ( false);

}, true, true);

