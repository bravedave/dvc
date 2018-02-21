<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	Creates a min combined file for css
		* requires a directory to write to
			=> requires appdir/app/public/css/ to be writable

		then you can call one file in place of several, if any css is updated, it will recompile it

	in theory - only used it once ...

	*/
NameSpace dvc;

abstract class cssmin {
	public static $debug = FALSE;
	public static $dvcmin = FALSE;
	public static $dvcminFiles = FALSE;

	protected static $rootPath = NULL;

	protected static function __createmin( $mindir, $cssmin, $files, $minify = FALSE) {
		$debug = self::$debug;
		//~ $debug = TRUE;

		if ( is_null( self::$rootPath))
			self::$rootPath = application::app()->getRootPath() . '/app/public/css';

		if ( $mindir)
			$outputDIR = sprintf( '%s/%s', self::$rootPath, $mindir);
		else
			$outputDIR = self::$rootPath;

		$output = sprintf( '%s/%s', $outputDIR, $cssmin);
		//~ sys::logger( $cssmin);
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
						sys::logger( 'cannot locate min.css file ' . $file);
						//~ sys::logger( realpath( $file));

					}

				}

				$content = implode("\n", $contents);
				if ( $minify) {
					$minifier = new \MatthiasMullie\Minify\CSS();
					$minifier->add( implode("\n", $contents));
					$content = $minifier->minify();

				}

				file_put_contents( $output, $content);
				return ( TRUE);
				//~ sys::logger( 'no of files = ' . count( $contents));

			}
			else {
				sys::logger( sprintf( '%s is not writable - cannot create a css.min.css here', $outputDIR));
				sys::logger( sprintf( 'please create a writable data folder : %s', $outputDIR ));
				sys::logger( sprintf( 'mkdir --mode=0777 %s', $outputDIR ));

			}

		}
		else {
			sys::logger( '[root]/app/public/ does not exist');

		}
		return ( FALSE);

	}

	public static function dvc( $minfile = NULL, $cssdir = NULL, $version = NULL) {
		$debug = self::$debug;
		//~ $debug = TRUE;

		$cssdir = (string)$cssdir;

		if ( (string)$version == '4') {
			self::$dvcminFiles = [
				'css/dvc-4.css',
				'css/brayworth.animation.css',
				'css/brayworth.components.css',
				'css/brayworth.context.css',
				'css/brayworth.growl.css',
				'css/brayworth.modal.css',
				'css/brayworth.utility.css'
			];
			if ( is_null( $minfile)) $minfile =  'dvc-4.min.css';

		}
		else {
			self::$dvcminFiles = [
				'css/dvc.css',
				'css/brayworth.animation.css',
				'css/brayworth.components.css',
				'css/brayworth.context.css',
				'css/brayworth.growl.css',
				'css/brayworth.modal.css',
				'css/brayworth.utility.css'
			];
			if ( is_null( $minfile)) $minfile =  'dvc.min.css';

		}

		$files = [];
		foreach( self::$dvcminFiles as $f)
			$files[] = __DIR__ . '/public/' . $f;

		if ( !application::app())
			throw new \Exceptions\ExternalUseViolation;

		if ( $cssdir) {
			self::$dvcmin = sprintf( '%scss/%s/%s?v=%s', \url::$URL, $cssdir, $minfile, \config::$VERSION );
			$cssmin = sprintf( '%s/app/public/css/%s/%s', application::app()->getRootPath(), $cssdir, $minfile);

		}
		else {
			self::$dvcmin = sprintf( '%scss/%s?vv=%s', \url::$URL, $minfile, \config::$VERSION );
			$cssmin = sprintf( '%s/app/public/css/%s', application::app()->getRootPath(), $minfile);

		}

		if ( realpath( $cssmin)) {

			if ( $debug) sys::logger( sprintf( 'css.min::dvc :: found :: %s', $cssmin));

			$modtime = 0;
			foreach ( $files as $file) {
				if ( realpath( $file))
					$modtime = max( [ $modtime, filemtime( $file)]);

				else
					sys::logger( 'cannot locate css.min file ' . $file);

			}

			$cssmodtime = filemtime( $cssmin);
			if ( $cssmodtime < $modtime) {
				if ( $debug) sys::logger( 'css.min::dvc :: latest mod time = ' . date( 'r', $modtime));
				if ( $debug) sys::logger( 'css.min::dvc :: you need to update ' . $cssmin);
				return ( self::__createmin( $cssdir, $minfile, $files, TRUE));

			}
			else {
				if ( $debug) sys::logger( 'css.min::dvc :: you have the latest version of ' . $cssmin);
				return ( TRUE);

			}

		}
		else {
			if ( $debug) sys::logger( sprintf( 'css.min::dvc :: not found :: %s - creating', $cssmin));
			return ( self::__createmin( $cssdir, $minfile, $files, TRUE));

		}

	}

}
