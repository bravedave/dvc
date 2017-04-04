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
	$debug = FALSE;
	//~ $debug = TRUE;

	$logFinalFailure = 0;

	if ( !$logFinalFailure) {
		$logFinalFailure = 1;	// don't do it and don't check again
		if ( preg_match( '@libs/vendor/autoloader.php$@', __FILE__)) {
			$logFinalFailure = 2;	// do it, because you are the brayworth lib version

		}

	}

	if ( strpos( $class, 'MailSo' ) !== FALSE) {
		if ( class_exists( 'CApi', FALSE)) {
			/** Don't continue and load MailSo - Afterlogic has their own version **/
			return FALSE;

		}

	}

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
	elseif ( preg_match( '/^f/', $class ) && $lib = realpath( __DIR__ . '/../flourishlib/' . str_replace('\\', '/', $class) . '.php')) {
		/* check to see if it's a flourish class and include it */
		include_once $lib;
		dvc\sys::logloader( sprintf( 'lib: %s', $lib ));
		return ( TRUE);

	}

	if ( $logFinalFailure == 2)
		dvc\sys::logger( sprintf( 'class not found: %s', $class ));

	return ( FALSE);

});

// require_once __DIR__ . '/../../../libs/vendor/autoloader.php';
