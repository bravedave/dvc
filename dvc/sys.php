<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/
NameSpace dvc;

abstract class sys {
	protected static $_loglevel = 1;
	protected static $_logloader = 0;
	protected static $_dbi = NULL;

	static $debug = FALSE;
	/**
	 * text2html: basically converts plain text to html by swaping in <br /> for \n
	 **/
	static function text2html( $inText, $maxrows = -1, $allAsteriskAsList = FALSE ) {

		if ( $maxrows > 0 ) {
			$a = array(
				"/\\\\n/",
				"/(\n)|(\\\\n)/"
			);
			$x = preg_split( "/\n/", $inText );
			while ( count( $x ) > ($maxrows+1) )
				array_pop( $x );
			$inText = implode( "<br />", $x );

		}

		$a = array(
			"/\r\n/",
			"/---\\n/",
			"/\\\\n/",
			"/\n/",
			"/$\*/" );

		$aR = array(
			"\n",
			'<hr align="left" style="width: 200px; margin: 0;" />',
			'<br />',
			'<br />',
			'<br />&bull;' );

		if ( $allAsteriskAsList ) {
			$a[] = "/\*/";
			$aR[] = "<br />&bull;";
			$inText = rtrim( $inText, " .*" );
		}

		return ( preg_replace( $a, $aR, $inText));

	}

	/**
	 * Debug logging
	 *	I just use 1-5, stuff fromthe application class is output if log level is 3
	 **/
	static function logging( $level = NULL ) {
		$oldLevel = self::$_loglevel;

		if ( !( is_null( $level )))
			self::$_loglevel = $level;

		return ( $oldLevel);

	}

	static function dbi() {
		if ( is_null( self::$_dbi))
			self::$_dbi = new dbi();

		return ( self::$_dbi);

	}

	static function logger( $v, $level = 0 ) {
		if ( (int)self::$_loglevel > 0 && $level <= (int)self::$_loglevel )
			error_log( $v );

	}

	static function logloaderon( $b) {
		if ( (bool)$b)
			self::$_logloader = 1;

	}

	static function logloader( $v) {
		if ( (bool)self::$_logloader)
			error_log( sprintf( '%d. %s', self::$_logloader++, $v));

	}

	static function logSQL( $v, $level = 0 ) {
		if ( (int)self::$_loglevel > 0 && $level <= (int)self::$_loglevel ) {
			self::logger( preg_replace( array( "@\r\n@","@\n@","@\t@","@\s\s*@" ), " ", $v ), $level);

		}

	}

	static function trace( $v ) {
		self::logger( $v);
		foreach ( debug_backtrace() as $e )
			self::logger( sprintf( '%s(%s)', $e['file'], $e['line'] ));

	}

	static function set_error_handler() {
		errsys::initiate( FALSE );
		//~ self::logger( 'Set Error Handler' );
		return;

		/**
		 * UnComment the return above to test this
		 **/

		try {
			trigger_error("First error", E_USER_NOTICE);
		}
		catch ( \Exception $e ) {
			print("Caught the error: ".$e->getMessage()."<br />\r\n" );
		}

		trigger_error("This event WILL fire", E_USER_NOTICE);
		trigger_error("This event will NOT fire", E_USER_NOTICE);

	}

	static function dump( $v, $title = '', $lExit = TRUE ) {
		if ( !$title) {
			if ( gettype( $v) == 'object')
				$title = get_class( $v);
			else
				$title = gettype( $v);

		}


		new html\dump( $v, $title );
		if ( $title == 'dvc\dbResult') {
			while ( $r = $v->dto())
				new html\dump( $r, get_class( $r));

		}

		if ( $lExit )
			exit;

	}

	static function mailer() {
		$mail = new \PHPMailer(); // defaults to using php "mail()"
		$mail->SetFrom( \config::$SUPPORT_EMAIL, \config::$SUPPORT_NAME);

		return ( $mail);

	}

	static function serve( $path) {
		if ( file_exists( $path)) {

			$path_parts = pathinfo( $path);
			$ext = strtolower( $path_parts['extension']);

			if ( $ext == "css" ) {
				Response::css_headers( filemtime( $path));
				readfile( $path);
				if ( self::$debug) \sys::logger( "served: $path");

			}
			elseif ( $ext == "js" ) {
				$expires = 0;
				if ( strstr( $path, 'jquery-'))
					$expires = \config::$JQUERY_EXPIRE_TIME;
				elseif ( strstr( $path, 'inputosaurus.js'))
					$expires = \config::$JQUERY_EXPIRE_TIME;
				elseif ( strstr( $path, 'tinylib.js'))
					$expires = \config::$JQUERY_EXPIRE_TIME;
				elseif ( strstr( $path, 'moment.min.js'))
					$expires = \config::$JQUERY_EXPIRE_TIME;
				elseif ( strstr( $path, 'bootstrap.min.js'))
					$expires = \config::$JQUERY_EXPIRE_TIME;
				elseif ( strstr( $path, 'brayworthlib.js'))
					$expires = \config::$JQUERY_EXPIRE_TIME;
				elseif ( strings::endswith( $path, '.js'))
					$expires = \config::$JS_EXPIRE_TIME;

				Response::javascript_headers( filemtime( $path), $expires);
				readfile( $path);
				if ( self::$debug) \sys::logger( "served: $path");

			}
			elseif ( $ext == "ico" ) {
				Response::icon_headers( filemtime( $path), \config::$CORE_IMG_EXPIRE_TIME);
				readfile( $path);
				if ( self::$debug) \sys::logger( "served: $path");

			}
			elseif ( $ext == "woff" || $ext == "woff2" ) {
				Response::headers('application/font-woff', filemtime( $path), \config::$FONT_EXPIRE_TIME);
				readfile( $path);
				if ( self::$debug) \sys::logger( "served: $path");

			}
			elseif ( $ext == "ttf" || $ext == "otf" ) {
				Response::headers('application/font-sfnt', filemtime( $path), \config::$FONT_EXPIRE_TIME);
				readfile( $path);
				if ( self::$debug) \sys::logger( "served: $path");

			}
			elseif ( $ext == "eot" ) {
				Response::headers('application/vnd.ms-fontobject', filemtime( $path), \config::$FONT_EXPIRE_TIME);
				readfile( $path);
				if ( self::$debug) \sys::logger( "served: $path");

			}
			elseif ( $ext == "png" ) {
				if ( strstr( $path, url::$URL . 'images/'))
					Response::png_headers( filemtime( $path), \config::$CORE_IMG_EXPIRE_TIME);
				else
					Response::png_headers( filemtime( $path));

				readfile( $path);
				if ( self::$debug) \sys::logger( "served: $path");

			}
			elseif ( $ext == "jpg" ) {
				if ( strstr( $path, url::$URL . 'images/'))
					Response::jpg_headers( filemtime( $path), \config::$CORE_IMG_EXPIRE_TIME);
				else
					Response::jpg_headers( filemtime( $path));
				readfile( $path);
				if ( self::$debug) \sys::logger( "served: $path");

			}
			elseif ( $ext == "txt" ) {
				Response::text_headers( filemtime( $path));
				readfile( $path);
				if ( self::$debug) \sys::logger( "served: $path");

			}
			elseif ( $ext == "gif" ) {
				if ( strstr( $path, url::$URL . 'images/'))
					Response::gif_headers( filemtime( $path), \config::$CORE_IMG_EXPIRE_TIME);
				else
					Response::gif_headers( filemtime( $path));
				readfile( $path);
				if ( self::$debug) \sys::logger( "served: $path");

			}
			elseif ( $ext == "svg" ) {
				/*
				 * maybe the expire time is like javascript rather than images - this is conservative */
				Response::headers('image/svg+xml', filemtime( $path), \config::$JS_EXPIRE_TIME);
				readfile( $path);
				if ( self::$debug) \sys::logger( "served: $path");

			}
			elseif ( $ext == "map" ) {
				Response::text_headers( filemtime( $path));
				readfile( $path);
				if ( self::$debug) \sys::logger( "served: $path");

			}
			elseif ( $ext == "json" ) {
				Response::json_headers( filemtime( $path));
				readfile( $path);
				if ( self::$debug) \sys::logger( "served: $path");

			}
			elseif ( $ext == "xml" ) {
				Response::xml_headers( filemtime( $path));
				readfile( $path);
				if ( self::$debug) \sys::logger( "served: $path");

			}
			elseif ( $ext == 'csv' ) {
				Response::csv_headers( $path_parts['basename'], filemtime( $path));
				readfile( $path);
				if ( self::$debug) \sys::logger( sprintf( 'served: %s', $path));

			}

		}
		elseif ( self::$debug) {
			\sys::logger( sprintf( 'not serving (not found): %s', $path));

		}

	}

}
