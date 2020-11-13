<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * Creates a min combined file for css
 * - requires a directory to write to
 * 		=> requires appdir/app/public/css/ to be writable
 *
 * then you can call one file in place of several,
 * if any css is updated, it will recompile it
 *
*/

namespace dvc;

abstract class cssmin {
	public static $debug = false;
	public static $dvcmin = false;
	public static $dvcminFiles = false;
	public static $dvc4Files = [
		'css/dvc-4.css',
		'css/brayworth.animation.css',
		'css/brayworth.autoResize.css',
		'css/brayworth.components.css',
		'css/brayworth.context.css',
		'css/brayworth.growl.css',
		'css/brayworth.markdown.css',
		'css/brayworth.modal-4.css',
		'css/brayworth.typeahead.css',
		'css/brayworth.utility.css'
	];

	protected static $rootPath = null;

	protected static function __createmin( $mindir, $cssmin, $files, $minify = false) {
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

	public static function dvc( $minfile = null, $cssdir = null, $version = null) {
		$debug = self::$debug;
		//~ $debug = TRUE;

		$cssdir = (string)$cssdir;

		if ( (string)$version == '4') {
			self::$dvcminFiles = self::$dvc4Files;
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

	protected static function _css_create( $options) {

		$input = [];

		if ( is_array( $options->cssFiles)) {
			foreach ( $options->cssFiles as $file) {
				if ( $options->debug) sys::logger( sprintf( '%s :: appending file %s', $options->libName, $file));
				$input[] = file_get_contents( realpath( $file));

			}

		}
		else {
			$gi = new GlobIterator( $options->cssFiles, FilesystemIterator::KEY_AS_FILENAME);

			foreach ($gi as $key => $item) {
				if ( $options->leadKey && $key == $options->leadKey) {
					if ( $options->debug) sys::logger( sprintf( '%s :: prepending leadKey %s', $options->libName, $options->leadKey));
					array_unshift( $input, file_get_contents( $item->getRealPath()));

				}
				else {
					if ( $options->debug) sys::logger( sprintf( '%s :: appending key %s', $options->libName, $key));
					$input[] = file_get_contents( $item->getRealPath());

				}

			}

		}

		$minifier = new \MatthiasMullie\Minify\CSS;
		$minifier->add( implode( "\n", $input));
		$content = $minifier->minify();

		file_put_contents( $options->libFile, $minifier->minify());

	}

	protected static function _css_serve( $options) {

		$expires = \config::$CSS_EXPIRE_TIME;
		$modTime = filemtime( $options->libFile);
		$age = time() - $modTime;
		if ( $age < 3600)
			$expires = 36;

		if ( $options->debug) sys::logger( sprintf( '%s :: serving(%s) %s', $options->libName, $expires, $options->libFile));
		Response::css_headers( filemtime( $options->libFile), $expires);
		print file_get_contents( $options->libFile);

	}

	public static function viewcss( $params) {
		$options = (object)array_merge([
			'debug' => false,
			'libName' => '',
			'leadKey' => false,
			'cssFiles' => false,
			'libFile' => false
		], $params);

		if ( $options->libFile ) {
			if ( $options->cssFiles ) {
				if ( file_exists( $options->libFile )) {
					/* test to see if requires update */
					$modtime = 0;
					if ( is_array( $options->cssFiles)) {
						foreach ( $options->cssFiles as $file)
							$modtime = max( [ $modtime, filemtime( realpath( $file))]);

					}
					else {
						$gi = new GlobIterator( $options->cssFiles, FilesystemIterator::KEY_AS_FILENAME);
						foreach ($gi as $key => $item)
							$modtime = max( [ $modtime, filemtime( $item->getRealPath())]);

					}

					$libmodtime = filemtime( $options->libFile);
					if ( $libmodtime < $modtime) {
						if ( $options->debug) sys::logger( sprintf( '%s :: updating %s, latest mod time = %s', $options->libName, $options->libFile, date( 'r', $modtime)));

						self::_css_create( $options);
						self::_css_serve( $options);

					}
					else {
						if ( $options->debug) sys::logger( sprintf( '%s :: latest version (%s)', $options->libName, $options->libFile));
							self::_css_serve( $options);

					}

				}
				else {
					/* create and serve */
					if ( $options->debug) sys::logger( sprintf( '%s :: creating %s', $options->libName, $options->libFile));

					self::_css_create( $options);
					self::_css_serve( $options);

				}

			} else { throw new \Exceptions\LibraryFilesNotSpecified; }

		} else { throw new \Exceptions\FileNotSpecified; }

	}

}
