<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/
namespace dvc;

abstract class sys {
	protected static $_loglevel = 1;
	protected static $_loaderCount = 0;
	protected static $_loaderCounter = null;
	protected static $_logloader = 0;
	protected static $_dbi = null;

	static $debug = false;

	static function dbi() {
		if ( is_null( self::$_dbi)) {
			if ( \config::$DB_TYPE == 'sqlite' ) {
				self::$_dbi = sqlite\db::instance();

			}
			else {
				self::$_dbi = new dbi();

			}

		}

		return ( self::$_dbi);

	}

	static function getTemplate( $template) {
		if ( $template) {
			if ( $template = preg_replace( '/[^\da-z_]/i', '', $template)) {
				$template .= '.html';

				$path = sprintf( '%s%sapp%stemplates%s%s',
					\application::app()->getRootPath(),
					DIRECTORY_SEPARATOR,
					DIRECTORY_SEPARATOR,
					DIRECTORY_SEPARATOR,
					$template);

				if ( file_exists( $path)) {
					\sys::serve( $path);

				}
				else {
					$path = sprintf( '%s%stemplates%s%s',
						__DIR__,
						DIRECTORY_SEPARATOR,
						DIRECTORY_SEPARATOR,
						$template);

					if ( file_exists( $path))
						\sys::serve( $path);

				}

			}

		}

	}

	static function logging( $level = NULL ) {
		/**
		 * Debug logging
		 *	I just use 1-5, stuff fromthe application class is output if log level is 3
		 **/
		$oldLevel = self::$_loglevel;

		if ( !( is_null( $level )))
			self::$_loglevel = $level;

		return ( $oldLevel);

	}

	static function logger( $v, $level = 0 ) {
		if ( (int)self::$_loglevel > 0 && $level <= (int)self::$_loglevel )
			error_log( $v );

	}

	static function logloaderon( $b) {
		self::$_logloader = (bool)$b;

	}

	static function loaderCounter( hitter $hitter) {
		self::$_loaderCounter = $hitter;

	}

	static function logloader( $v) {
		self::$_loaderCount++;

		if ( self::$_loaderCounter)
			self::$_loaderCounter->hits( self::$_loaderCount);

		if ( (bool)self::$_logloader)
			error_log( sprintf( '%d. %s', self::$_loaderCount, $v));

	}

	static function logSQL( $v, $level = 0 ) {
		if ( (int)self::$_loglevel > 0 && $level <= (int)self::$_loglevel ) {
			self::logger( preg_replace( array( "@\r\n@","@\n@","@\t@","@\s\s*@" ), " ", $v ), $level);

		}

	}

	static function text2html( $inText, $maxrows = -1, $allAsteriskAsList = FALSE ) {
		/**
		 * text2html: converts plain text to html by swaping in <br /> for \n
		 *
		 * $inText : text to be converted
		 * $maxRows : the number of rows to convert - default -1 == all
		 * $allAsteriskAsList : convert * (asterisk) to list (<ul><li>{text}</li></ul>)
		 **/

		if ( $maxrows > 0 ) {
			$a = [
				"/\\\\n/",
				"/(\n)|(\\\\n)/"
			];
			$x = preg_split( "/\n/", $inText );
			while ( count( $x ) > ($maxrows+1) )
				array_pop( $x );
			$inText = implode( "<br />", $x );

		}

		$a = [
			"/\r\n/",
			"/---\\n/",
			"/\\\\n/",
			"/\n/",
			"/$\*/"
		];

		$aR = [
			"\n",
			'<hr align="left" style="width: 200px; margin: 0;" />',
			'<br />',
			'<br />',
			'<br />&bull;'
		];

		if ( $allAsteriskAsList ) {
			$a[] = "/\*/";
			$aR[] = "<br />&bull;";
			$inText = rtrim( $inText, " .*" );
		}

		return ( preg_replace( $a, $aR, $inText));

	}

	static function trace( $v, $level = 0 ) {
		self::logger( $v);
		$level = (int)$level;
		$iLevel = 0;
		foreach ( debug_backtrace() as $e ) {
			self::logger( sprintf( '%s(%s)', $e['file'], $e['line'] ));
			if ( $level > 0 && ++$iLevel > $level ) {
				break;

			}

		}

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

		if ( $title == 'dvc\dbResult' || $title == 'dvc\sqlite\dbResult') {
			printf( '<h1>%s</h1>', $title);
			while ( $r = $v->dto())
				new html\dump( $r, get_class( $r));

		}
		else {
			new html\dump( $v, $title );

		}

		if ( $lExit )
			exit;

	}

	static function isWindows() {
		return ( 'WIN' === strtoupper(substr(PHP_OS, 0, 3)));

	}

	static function mailer() {
		if (self::isWindows()) {

			$mail = new \PHPMailer; // use smtp with server set to mail

			$mail->isSMTP();

			/*
			* This is weighted to my own enviroment
			*
			* It probably should be more dynamic
			*
			*/
			$mail->Host = 'mail';
			$mail->Port = 25;
			$mail->SMTPSecure = 'tls';
			$mail->SMTPOptions = [
				'ssl' => [
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true

				]

			];

		}
		else {
			$mail = new \PHPMailer; // defaults to using php "mail()"

		}

		$mail->SetFrom( \config::$SUPPORT_EMAIL, \config::$SUPPORT_NAME);
		return ( $mail);

	}

	static function serve( $path) {
		if ( file_exists( $path)) {

			$serve = [
				'avi' => 'video/x-msvideo',
				'doc' => 'application/msword',
				'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
				'map' => 'text/plain',
				'mp4' => 'video/mp4',
				'mov' => 'video/quicktime',
				'txt' => 'text/plain',
				'xls' => 'application/vnd.ms-excel',
				'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			];

			$path_parts = pathinfo( $path);
			$ext = strtolower( $path_parts['extension']);

			if ( $ext == 'css' ) {
				Response::css_headers( filemtime( $path));
				readfile( $path);
				if ( self::$debug) \sys::logger( "served: $path");

			}
			elseif ( $ext == 'js' ) {
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
			elseif ( $ext == 'eot' ) {
				Response::headers('application/vnd.ms-fontobject', filemtime( $path), \config::$FONT_EXPIRE_TIME);
				readfile( $path);
				if ( self::$debug) \sys::logger( "served: $path");

			}
			elseif ( $ext == 'ico' ) {
				Response::icon_headers( filemtime( $path), \config::$CORE_IMG_EXPIRE_TIME);
				readfile( $path);
				if ( self::$debug) \sys::logger( "served: $path");

			}
			elseif ( $ext == 'png' ) {
				if ( strstr( $path, url::$URL . 'images/'))
				Response::png_headers( filemtime( $path), \config::$CORE_IMG_EXPIRE_TIME);
				else
				Response::png_headers( filemtime( $path));

				readfile( $path);
				if ( self::$debug) \sys::logger( "served: $path");

			}
			elseif ( $ext == 'ttf' || $ext == 'otf' ) {
				Response::headers('application/font-sfnt', filemtime( $path), \config::$FONT_EXPIRE_TIME);
				readfile( $path);
				if ( self::$debug) \sys::logger( "served: $path");

			}
			elseif ( $ext == 'woff' || $ext == 'woff2' ) {
				Response::headers('application/font-woff', filemtime( $path), \config::$FONT_EXPIRE_TIME);
				readfile( $path);
				if ( self::$debug) \sys::logger( "served: $path");

			}
			elseif ( $ext == 'jpg'|| $ext == 'jpeg' ) {
				if ( strstr( $path, url::$URL . 'images/')) {
					Response::jpg_headers( filemtime( $path), \config::$CORE_IMG_EXPIRE_TIME);

				}
				else {
					Response::jpg_headers( filemtime( $path));

				}
				readfile( $path);
				if ( self::$debug) \sys::logger( "served: $path");

			}
			elseif ( $ext == 'gif' ) {
				if ( strstr( $path, url::$URL . 'images/'))
				Response::gif_headers( filemtime( $path), \config::$CORE_IMG_EXPIRE_TIME);
				else
				Response::gif_headers( filemtime( $path));
				readfile( $path);
				if ( self::$debug) \sys::logger( "served: $path");

			}
			elseif ( $ext == 'svg' ) {
				/*
				* maybe the expire time is like javascript rather than images - this is conservative */
				Response::headers('image/svg+xml', filemtime( $path), \config::$JS_EXPIRE_TIME);
				readfile( $path);
				if ( self::$debug) \sys::logger( "served: $path");

			}
			elseif ( $ext == 'json' ) {
				Response::json_headers( filemtime( $path));
				readfile( $path);
				if ( self::$debug) \sys::logger( "served: $path");

			}
			elseif ( $ext == 'xml' ) {
				Response::xml_headers( filemtime( $path));
				readfile( $path);
				if ( self::$debug) \sys::logger( "served: $path");

			}
			elseif ( $ext == 'csv' ) {
				Response::csv_headers( $path_parts['basename'], filemtime( $path));
				readfile( $path);
				if ( self::$debug) \sys::logger( sprintf( 'served: %s', $path));

			}
			elseif ( $ext == 'pdf' ) {
				Response::pdf_headers( $path_parts['basename'], filemtime( $path));
				readfile( $path);
				if ( self::$debug) \sys::logger( sprintf( 'served: %s', $path));

			}
			elseif ( $ext == 'tif' || $ext == 'tiff' ) {
				Response::tiff_headers( $path_parts['basename'], filemtime( $path));
				readfile( $path);
				if ( self::$debug) \sys::logger( sprintf( 'served: %s', $path));

			}
			elseif ( $ext == 'zip' ) {
				Response::zip_headers( $path_parts['basename'], filemtime( $path));
				readfile( $path);
				if ( self::$debug) \sys::logger( sprintf( 'served: %s', $path));

			}
			elseif ( $ext == 'html' ) {
				Response::html_headers( $path_parts['basename'], filemtime( $path));
				readfile( $path);
				if ( self::$debug) \sys::logger( sprintf( 'served: %s', $path));

			}
			elseif ( isset( $serve[ $ext])) {
				Response::headers($serve[ $ext], filemtime( $path));
				readfile( $path);
				if ( self::$debug) \sys::logger( sprintf( 'served %s from %s', $serve[ $ext], $path));

			}
			elseif ( self::$debug) {
				\sys::logger( sprintf( 'not serving (file type not served): %s', $path));

			}

		}
		elseif ( self::$debug) {
			\sys::logger( sprintf( 'not serving (not found): %s', $path));

		}

	}

	public function serveBootStrap( $type = 'css') {
		if (\config::$BOOTSTRAP_REQUIRE_POPPER) {
			if ( 'css' == $type) {
				$lib = __DIR__ . '/bootstrap4/css/bootstrap.min.css';
				self::serve( $lib);

			}
			elseif ( 'js' == $type) {
				$files = [
					__DIR__ . '/bootstrap4/js/bootstrap.js',
					__DIR__ . '/bootstrap4/js/popper.js',

				];

				jslib::viewjs([
					'debug' => false,
					'libName' => 'bootstrap4',
					'jsFiles' => $files,
					'libFile' => config::tempdir()  . '_bootstrap4_tmp.js'

				]);


			}

		}
		else {
			$root = realpath( __DIR__ . '/../../../twbs');
			if ( $root) {
				$path = realpath( sprintf( '%s/bootstrap/dist', $root));
				if ( 'css' == $type) {
					$lib = sprintf( '%s/css/bootstrap.min.css',$path);
					self::serve( $lib);

				}
				elseif ( 'js' == $type) {
					$lib = sprintf( '%s/js/bootstrap.bundle.min.js',$path);
					// self::logger( $lib);
					self::serve( $lib);

				}

			}
			else {
				throw new \Exception( 'Cannot locate twbs bootstrap - install with compose require twbs/bootstrap');

			}

		}

	}

}
