<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	Creates a lib combined file for a js library
		* requires a directory to write to -see tinymce for example
			=> requires appdir/app/public/js/tinymce to be writable

		then you can call one file in place of several, if the library is updated, it will recompile it

	in theory - only used it once ...

	*/
NameSpace dvc;

abstract class jslib {
	public static $debug = FALSE;
	public static $tinylib = FALSE;
	public static $brayworthlib = FALSE;
	public static $reactlib = FALSE;

	protected static $rootPath = NULL;

	protected static function __createlib( $libdir, $jslib, $files, $minify = FALSE) {
		$debug = self::$debug;
		//~ $debug = TRUE;

		if ( is_null( self::$rootPath))
			self::$rootPath = application::app()->getRootPath() . '/app/public/js';

		if ( $libdir)
			$outputDIR = sprintf( '%s/%s', self::$rootPath, $libdir);
		else
			$outputDIR = self::$rootPath;

		$output = sprintf( '%s/%s', $outputDIR, $jslib);
		//~ sys::logger( $jslib);
		//~ sys::logger( $output);

		//~ return ( FALSE);

		if ( file_exists( application::app()->getRootPath() . '/app/public/' )) {
			if ( !( file_exists( $outputDIR)) && is_writable( self::$rootPath)) {
				mkdir( $outputDIR, 0777, TRUE);
				chmod( $outputDIR, 0777);

			}

			if ( is_writable( $outputDIR)) {
				$contents = array();
				foreach ( $files as $file) {
					if ( realpath( $file)) {
						$contents[] = file_get_contents( $file);

					}
					else {
						sys::logger( 'cannot locate library file ' . $file);
						//~ sys::logger( realpath( $file));

					}

				}

				$content = implode("\n", $contents);
				if ( $minify) {
					$minifier = new \MatthiasMullie\Minify\JS();
					$minifier->add( implode("\n", $contents));
					$content = $minifier->minify();

				}

				file_put_contents( $output, $content);
				return ( TRUE);
				//~ sys::logger( 'no of files = ' . count( $contents));

			}
			else {
				sys::logger( sprintf( '%s is not writable - cannot create a library here', $outputDIR));
				sys::logger( sprintf( 'please create a writable data folder : %s', $outputDIR ));
				sys::logger( sprintf( 'mkdir --mode=0777 %s', $outputDIR ));

			}

		}
		else {
			sys::logger( application::app()->getRootPath() . '/app/public/ does not exist');

		}
		return ( FALSE);

	}

	public static function tinymce( $lib = 'tinylib.js', $libdir = 'tinymce') {
		$debug = self::$debug;
		//~ $debug = TRUE;

		$files = array(
			__DIR__ . '/public/js/tinymce/tinymce.min.js',
			__DIR__ . '/public/js/tinymce/themes/modern/theme.min.js',
			__DIR__ . '/public/js/tinymce/plugins/autolink/plugin.min.js',
			__DIR__ . '/public/js/tinymce/plugins/paste/plugin.min.js',
			__DIR__ . '/public/js/tinymce/plugins/lists/plugin.min.js',
			__DIR__ . '/public/js/tinymce/plugins/table/plugin.min.js',
			__DIR__ . '/public/js/tinymce/plugins/colorpicker/plugin.min.js',
			__DIR__ . '/public/js/tinymce/plugins/textcolor/plugin.min.js' );

		if ( !application::app()) {
			sys::logger( 'you cannot use this external to application()');
			throw new \Exception( 'you cannot use this external to application()');
			return ( FALSE);

		}

		self::$tinylib = sprintf( '%sjs/%s/%s?v=%s', \url::$URL, $libdir, $lib, \config::$VERSION );
		$jslib = sprintf( '%s/app/public/js/%s/%s', application::app()->getRootPath(), $libdir, $lib);
		if ( realpath( $jslib)) {

			if ( $debug) sys::logger( sprintf( 'jslib::tinymce found :: %s', $jslib));

			$modtime = 0;
			foreach ( $files as $file) {
				if ( realpath( $file))
					$modtime = max( array( $modtime, filemtime( $file)));

				else
					sys::logger( 'cannot locate library file ' . $file);

			}

			$libmodtime = filemtime( $jslib);
			if ( $libmodtime < $modtime) {
				if ( $debug) sys::logger( 'latest mod time = ' . date( 'r', $modtime));
				if ( $debug) sys::logger( 'you need to update ' . $jslib);

				return ( self::__createlib( $libdir, $lib, $files));

			}
			else {
				if ( $debug) sys::logger( 'you have the latest version of ' . $jslib);

				return ( TRUE);

			}

		}
		else {
			if ( $debug) sys::logger( sprintf( 'jslib::tinymce not found :: %s - creating', $jslib));
			return ( self::__createlib( $libdir, $lib, $files));

		}

	}

	public static function brayworth( $lib = 'brayworthlib.js', $libdir = '') {
		$debug = self::$debug;
		//~ $debug = TRUE;

		$files = array(
			__DIR__ . '/public/js/jquery.visible.js',
			__DIR__ . '/public/js/brayworth.js',
			__DIR__ . '/public/js/brayworth-context.js',
			__DIR__ . '/public/js/js.cookie.js' );

		if ( !application::app()) {
			sys::logger( 'you cannot use this external to application()');
			throw new \Exception( 'you cannot use this external to application()');
			return ( FALSE);

		}

		if ( $libdir) {
			self::$brayworthlib = sprintf( '%sjs/%s/%s?v=', \url::$URL, $libdir, $lib);
			$jslib = sprintf( '%s/app/public/js/%s/%s', application::app()->getRootPath(), $libdir, $lib);

		}
		else {
			self::$brayworthlib = sprintf( '%sjs/%s?vv=', \url::$URL, $lib);
			$jslib = sprintf( '%s/app/public/js/%s', application::app()->getRootPath(), $lib);

		}

		if ( realpath( $jslib)) {

			if ( $debug) sys::logger( sprintf( 'jslib::brayworth :: found :: %s', $jslib));

			$modtime = 0;
			foreach ( $files as $file) {
				if ( realpath( $file))
					$modtime = max( array( $modtime, filemtime( $file)));

				else
					sys::logger( 'cannot locate library file ' . $file);

			}

			$libmodtime = filemtime( $jslib);
			if ( $libmodtime < $modtime) {
				if ( $debug) sys::logger( 'jslib::brayworth :: latest mod time = ' . date( 'r', $modtime));
				if ( $debug) sys::logger( 'jslib::brayworth :: you need to update ' . $jslib);
				if ( self::__createlib( $libdir, $lib, $files, TRUE)) {
					$version = filemtime( $jslib);
					self::$brayworthlib .= $version;

					return ( TRUE);

				}

			}
			else {
				if ( $debug) sys::logger( 'jslib::brayworth :: you have the latest version of ' . $jslib);

				$version = filemtime( $jslib);
				self::$brayworthlib .= $version;

				return ( TRUE);

			}

		}
		else {
			if ( $debug) sys::logger( sprintf( 'jslib::brayworth :: not found :: %s - creating', $jslib));
			if ( self::__createlib( $libdir, $lib, $files, TRUE)) {
				$version = filemtime( $jslib);
				self::$brayworthlib .= $version;

			}

		}

		return ( FALSE);

	}

	public static function react( $lib = 'reactlib.js', $libdir = '') {
		$debug = self::$debug;
		//~ $debug = TRUE;

		$files = array(
			__DIR__ . '/public/js/react.min.js',
			__DIR__ . '/public/js/react-dom.min.js' );

		if ( !application::app()) {
			sys::logger( 'you cannot use this external to application()');
			throw new \Exception( 'you cannot use this external to application()');
			return ( FALSE);

		}

		if ( $libdir) {
			self::$reactlib = sprintf( '%sjs/%s/%s?v=%s', \url::$URL, $libdir, $lib, \config::$VERSION );
			$jslib = sprintf( '%s/app/public/js/%s/%s', application::app()->getRootPath(), $libdir, $lib);

		}
		else {
			self::$reactlib = sprintf( '%sjs/%s?vv=%s', \url::$URL, $lib, \config::$VERSION );
			$jslib = sprintf( '%s/app/public/js/%s', application::app()->getRootPath(), $lib);

		}

		if ( realpath( $jslib)) {

			if ( $debug) sys::logger( sprintf( 'jslib::react :: found :: %s', $libdir, $jslib));

			$modtime = 0;
			foreach ( $files as $file) {
				if ( realpath( $file))
					$modtime = max( array( $modtime, filemtime( $file)));

				else
					sys::logger( 'cannot locate library file ' . $file);

			}

			$libmodtime = filemtime( $jslib);
			if ( $libmodtime < $modtime) {
				if ( $debug) sys::logger( 'jslib::reactlib :: latest mod time = ' . date( 'r', $modtime));
				if ( $debug) sys::logger( 'jslib::reactlib :: you need to update ' . $jslib);
				return ( self::__createlib( $libdir, $lib, $files));	// not minified

			}
			else {
				if ( $debug) sys::logger( 'jslib::reactlib :: you have the latest version of ' . $jslib);
				return ( TRUE);

			}

		}
		else {
			if ( $debug) sys::logger( sprintf( 'jslib::reactlib :: not found :: %s - creating', $jslib));
			return ( self::__createlib( $libdir, $lib, $files));	// not minified

		}

	}

}
