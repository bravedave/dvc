<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	This is basically the system, when a class is not found, it routes through here
	/app/ is checked before /lib/ which means that classes in lib can be overwritten

	DO NOT change this file
	Copy it to <application>/vendor/ and modify it there

	it is probably sufficient to:
		a. copy this file into the <application>/vendor folder
		b. uncomment the last line of this file
	*/

spl_autoload_register(function ($class) {
	if ( $lib = realpath( __DIR__ . '/../app/' . str_replace('\\', '/', $class) . '.php')) {
		include_once $lib;
		dvc\sys::logloader( sprintf( 'app: %s', $lib ));

		return ( TRUE);

	}
	elseif ( $lib = realpath( __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php')) {
		include_once $lib;
		dvc\sys::logloader( sprintf( 'lib: %s', $lib ));
		return ( TRUE);

	}
	return ( FALSE);

});

require_once __DIR__ . '/../../autoload.php';
