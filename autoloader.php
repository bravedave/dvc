<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

include_once __DIR__ . '/src/dvc/utility.php';
dvc\core\application::load_dvc_autoloader_fallback();

// spl_autoload_register( function ($class) {
// 	if ( $lib = realpath( implode([
// 		__DIR__,
// 		DIRECTORY_SEPARATOR,
// 		'src',
// 		DIRECTORY_SEPARATOR,
// 		str_replace('\\', '/', $class),
// 		'.php'

// 		]))) {

// 		include_once $lib;
// 		dvc\core\load::logger( sprintf( 'lib: %s', $lib ));
// 		return ( true);

// 	}
// 	return ( false);

// });

if ( file_exists( $vendor = __DIR__ . '/vendor/autoload.php')) {
	require_once $vendor;

}
elseif ( file_exists( $vendor = __DIR__ . '/../../autoload.php')) {
	require_once $vendor;

}

\sys::logger( sprintf('<deprecation - not required> %s', __FILE__));
