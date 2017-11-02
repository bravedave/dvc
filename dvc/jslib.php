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
	public static $brayworthlibFiles = FALSE;

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
				$contents = [];
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
			sys::logger( '[root]/app/public/ does not exist');

		}
		return ( FALSE);

	}

	public static function tinymce( $lib = 'tinylib.js', $libdir = 'tinymce', $plugins = 'autolink,paste,lists,table,colorpicker,textcolor') {
		$debug = self::$debug;
		//~ $debug = TRUE;

		$files = [
			__DIR__ . '/public/js/tinymce/tinymce.min.js',
			__DIR__ . '/public/js/tinymce/themes/modern/theme.min.js' ];

		foreach( explode( ',', $plugins) as $plugin)
			$files[] = sprintf( '%s/public/js/tinymce/plugins/%s/plugin.min.js', __DIR__, trim( $plugin));

		if ( $debug) {
			foreach ( $files as $file)
				sys::logger( 'tinylib file: ' . $file);

		}

		if ( !application::app())
			throw new \Exceptions\ExternalUseViolation;

		self::$tinylib = sprintf( '%sjs/%s/%s?v=', \url::$URL, $libdir, $lib);
		$jslib = sprintf( '%s/app/public/js/%s/%s', application::app()->getRootPath(), $libdir, $lib);
		if ( realpath( $jslib)) {

			if ( $debug) sys::logger( sprintf( 'jslib::tinymce found :: %s', $jslib));

			$modtime = 0;
			foreach ( $files as $file) {
				if ( realpath( $file))
					$modtime = max( [ $modtime, filemtime( $file)]);

				else
					sys::logger( 'cannot locate library file ' . $file);

			}

			$libmodtime = filemtime( $jslib);
			if ( $libmodtime < $modtime) {
				if ( $debug) sys::logger( 'latest mod time = ' . date( 'r', $modtime));
				if ( $debug) sys::logger( 'you need to update ' . $jslib);

				if  ( self::__createlib( $libdir, $lib, $files)) {
					$version = filemtime( $jslib);
					self::$tinylib .= $version;

					return ( TRUE);

				}

			}
			else {
				if ( $debug) sys::logger( 'you have the latest version of ' . $jslib);

				$version = filemtime( $jslib);
				self::$tinylib .= $version;

				return ( TRUE);

			}

		}
		else {
			if ( $debug) sys::logger( sprintf( 'jslib::tinymce not found :: %s - creating', $jslib));
			if ( self::__createlib( $libdir, $lib, $files)) {
				$version = filemtime( $jslib);
				self::$tinylib .= $version;

				return ( TRUE);

			}

		}

	}

	public static function brayworth( $lib = 'brayworthlib.js', $libdir = '') {
		$debug = self::$debug;
		//~ $debug = TRUE;

		self::$brayworthlibFiles = [
			'js/jquery.visible.js',
			'js/_brayworth_.js',
			'js/_brayworth_.bootstrapModalPop.js',
			'js/_brayworth_.browser.js',
			'js/_brayworth_.context.js',
			'js/_brayworth_.growl.js',
			'js/_brayworth_.fileDragDropHandler.js',
			'js/_brayworth_.hashScroll.js',
			'js/_brayworth_.InitHRefs.js',
			'js/_brayworth_.initDatePickers.js',
			'js/_brayworth_.lazyImageLoader.js',
			'js/_brayworth_.logonModal.js',
			'js/_brayworth_.modal.js',
			'js/_brayworth_.modalDialog.js',
			'js/_brayworth_.swipe.js',
			'js/_brayworth_.strings.js',
			'js/brayworth.js',
			'js/templation.js',
			'js/js.cookie.js' ];

		$files = [];
		foreach( self::$brayworthlibFiles as $f)
			$files[] = __DIR__ . '/public/' . $f;

		if ( !application::app())
			throw new \Exceptions\ExternalUseViolation;

		if ( $libdir) {
			self::$brayworthlib = sprintf( '%sjs/%s/%s?v=', \url::$URL, $libdir, $lib);
			$jslib = sprintf( '%s/app/public/js/%s/%s', application::app()->getRootPath(), $libdir, $lib);

		}
		else {
			self::$brayworthlib = sprintf( '%sjs/%s?vv=', \url::$URL, $lib);
			$jslib = sprintf( '%s/app/public/js/%s', application::app()->getRootPath(), $lib);

		}

		if ( realpath( $jslib) && file_exists( $jslib)) {

			if ( $debug) sys::logger( sprintf( 'jslib::brayworth :: found :: %s', $jslib));

			$modtime = 0;
			foreach ( $files as $file) {
				if ( realpath( $file))
					$modtime = max( [ $modtime, filemtime( $file)]);

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

				return ( TRUE);

			}

		}

		return ( FALSE);

	}

}
