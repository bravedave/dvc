<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/

spl_autoload_register(function ($class) {
	if ( $lib = realpath( __DIR__ . '/' . str_replace('\\', '/', $class) . '.php')) {
		include_once $lib;
		dvc\sys::logloader( sprintf( 'lib: %s', $lib ));
		return ( TRUE);

	}
	return ( FALSE);

});

/*
* upstream the autoload here by including some local file
* autoload-local-path is included in .gitignore
*/
if ( file_exists( __DIR__ . 'autoload-local-path.php')) {
	include __DIR__ . 'autoload-local-path.php';

}
