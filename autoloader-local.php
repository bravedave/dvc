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
	if ( $lib = realpath( __DIR__ . '/' . str_replace('\\', '/', $class) . '.php')) {
		include_once $lib;
		dvc\core\load::logger( sprintf( 'lib: %s', $lib ));
		return ( true);

	}

	return ( false);

});


/*
* upstream the autoload here by including some local file
* autoload-local-path is excluded in .gitignore
*/
if ( file_exists( __DIR__ . DIRECTORY_SEPARATOR . 'autoload-local-path.php')) {
	include __DIR__ . DIRECTORY_SEPARATOR . 'autoload-local-path.php';

}
