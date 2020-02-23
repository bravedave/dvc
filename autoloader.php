<?php
/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * This work is licensed under a Creative Commons Attribution 4.0 International Public License.
 * 	http://creativecommons.org/licenses/by/4.0/
 *
 * DO NOT change this file
 * Copy it to <application>/app and modify it there
 */

spl_autoload_register( function ($class) {
	if ( $lib = realpath( implode([
		__DIR__,
		DIRECTORY_SEPARATOR,
		'src',
		DIRECTORY_SEPARATOR,
		str_replace('\\', '/', $class),
		'.php'

		]))) {

		include_once $lib;
		dvc\core\load::logger( sprintf( 'lib: %s', $lib ));
		return ( true);

	}
	return ( false);

});

if ( file_exists( $vendor = __DIR__ . '/vendor/autoload.php')) {
	require_once $vendor;

}
elseif ( file_exists( $vendor = __DIR__ . '/../../autoload.php')) {
	require_once $vendor;

}
