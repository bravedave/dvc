<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc;

abstract class sys {
	protected static $_loglevel = 1;
	protected static $_dbi = null;

	static $debug = false;

	static function dbi() {
		if ( is_null( self::$_dbi)) {
			if ( \config::$DB_TYPE == 'sqlite' ) {
				self::$_dbi = sqlite\db::instance();

			}
			else {
				self::$_dbi = new dbi;

			}

		}

		return ( self::$_dbi);

	}

	static function dbCheck( string $file) {
        return 'sqlite' == \config::$DB_TYPE ?
            new sqlite\dbCheck( self::dbi(), $file ) :
            new \dao\dbCheck( self::dbi(), $file );

	}

	static function dbCachePrefix() {
		if ( \config::$DB_CACHE_PREFIX) {
			return \config::$DB_CACHE_PREFIX;

		}
    elseif ( 'mysql' == \config::$DB_TYPE ) {
      return \config::$DB_NAME;

		}
		else {
			$path = implode( DIRECTORY_SEPARATOR, [
				\config::dataPath(),
				'dbCachePrefix.json'

			]);

			if ( \file_exists( $path)) {
				$j = \json_decode( \file_get_contents( $path));
				\config::$DB_CACHE_PREFIX = $j->prefix;
				return \config::$DB_CACHE_PREFIX;

			}
			else {
				$a = (object)[ 'prefix' => bin2hex( random_bytes( 6)) ];
				\file_put_contents( $path, \json_encode( $a));
				\config::$DB_CACHE_PREFIX = $a->prefix;
				return \config::$DB_CACHE_PREFIX;

			}

		}

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

	static function logging( $level = null) {
		/**
		 * Debug logging
		 *	I just use 1-5, stuff fromthe application class is output if log level is 3
		 **/
		$oldLevel = self::$_loglevel;

		if ( !( is_null( $level )))
			self::$_loglevel = $level;

		return ( $oldLevel);

	}

	static function logger( $v, $level = 0) {
		if ( (int)self::$_loglevel > 0 && $level <= (int)self::$_loglevel ) {
			error_log( $v );

		}

	}

	static function logloaderon( $b) {
		error_log( sprintf( 'deprecated : %s', self::traceCaller()));

	}

	static function loaderCounter( hitter $hitter) {
		error_log( sprintf( 'deprecated : %s', self::traceCaller()));

	}

	static function logloader( $v) {
		error_log( sprintf( 'deprecated : %s : %s', $v, self::traceCaller()));

	}

	static function logSQL( $v, $level = 0 ) {
		self::logger( preg_replace( [ "@\r\n@","@\n@","@\t@","@\s\s*@" ], ' ', $v ));

	}

  protected static $_options = [];

	protected static function _options_file() {
		return implode( DIRECTORY_SEPARATOR, [
      rtrim( config::dataPath(), '/ '),
      'sys.json'

    ]);

	}

  static function option( $key, $val = null ) {
		$debug = false;
		// $debug = true;

		if ( !self::$_options) {
      if ( file_exists( $config = self::_options_file())) {
        self::$_options = (array)json_decode( file_get_contents( $config));

      }

		}

		$ret = '';
		if ( self::$_options) {
      /* return the existing value */
      if ( isset( self::$_options[$key])) {
        $ret = (string)self::$_options[$key];

        if ( $debug) self::logger( sprintf( 'retrieve option value : %s = %s', $key, $ret));

      } elseif ( $debug) { self::logger( sprintf( 'retrieve option value (default - not set) : %s = %s', $key, $ret)); }

		} elseif ( $debug) { self::logger( sprintf( 'retrieve option value (null): %s = %s', $key, $ret)); }


		if ( !is_null( $val)) {

			/* writer */
			if ( (string)$val == '') {
				if ( isset( self::$_options[$key])) {
					unset( self::$_options[$key]);

        }

			}
			else {
				self::$_options[$key] = (string)$val;

			}

      file_put_contents(
        self::_options_file(),
        json_encode(
          self::$_options,
          JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT

        )

      );

		}

		return ( $ret);

  }

  static function set_error_handler() {
		errsys::initiate( false );

	}

	static function text2html( $inText, $maxrows = -1, $allAsteriskAsList = false) {
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
			if ( isset( $e['file'])) {
				self::logger( sprintf( '%s(%s)', $e['file'], $e['line'] ));

			}
			else {
				self::logger( print_r( $e, true));

			}

			if ( $level > 0 && ++$iLevel > $level ) {
				break;

			}

		}

	}

	static function traceCaller() {
		$trace = debug_backtrace();
		if ( isset( $trace[2])) {
			$caller = $trace[2];
			if (isset($caller['class'])) {
				return sprintf( '%s/%s', $caller['class'], $caller['function'] );

			}

			return $caller['function'];

		}

		return 'unknown caller';

	}

	static function dump( $v, $title = '', $lExit = true) {
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
		$mail = new \PHPMailer;
		$mail->XMailer = 'BrayWorth DVC Mailer 1.0.0 (https://brayworth.com/)';

		if (self::isWindows()) {
			$mail->isSMTP(); // use smtp with server set to mail

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

            $mailconfig = sprintf( '%s/mail-config.json', trim( \config::dataPath(), '/ '));
            if ( file_exists( $mailconfig)) {
                $_mc = json_decode( file_get_contents( $mailconfig));

                if ( isset( $_mc->Host)) $mail->Host = $_mc->Host;

                if ( isset( $_mc->Port)) $mail->Port = $_mc->Port;

                if ( isset( $_mc->SMTPSecure)) $mail->SMTPSecure = $_mc->SMTPSecure;

                if ( isset( $_mc->SMTPOptions)) {
                    if ( isset( $_mc->SMTPOptions->ssl)) {
                        $mail->SMTPOptions = [
                            'ssl' => (array)$_mc->SMTPOptions->ssl

                        ];

                    }

                }

                if ( isset( $_mc->SMTPUserName) && isset( $_mc->SMTPPassword)) {
					$mail->SMTPAuth = true;
					$mail->Username = $_mc->SMTPUserName;
					$mail->Password = $_mc->SMTPPassword;

				}

            }
            else {
                file_put_contents( $mailconfig, json_encode((object)[
                    'Host' => $mail->Host,
                    'Port' => $mail->Port,
                    'SMTPSecure' => $mail->SMTPSecure,
                    'SMTPOptions' => $mail->SMTPOptions

				], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            }

		}

		$mail->setFrom( \config::$SUPPORT_EMAIL, \config::$SUPPORT_NAME);
		return ( $mail);

	}

	static public function serve( $path) {
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
			if ( isset( $path_parts['extension'])) {
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
					if ( strstr( $path, url::$URL . 'images/')) {
						Response::gif_headers( filemtime( $path), \config::$CORE_IMG_EXPIRE_TIME);

					}
					else {
						Response::gif_headers( filemtime( $path));

					}
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
			else {
				\sys::logger( sprintf( 'not serving : %s', $path));

			}

		}
		elseif ( self::$debug) {
			\sys::logger( sprintf( 'not serving (not found): %s', $path));

		}

	}

	static public function serveBootStrap( $type = 'css') {
		if ( \config::$BOOTSTRAP_REQUIRE_POPPER) {
      \sys::logger( sprintf('deprecated : $BOOTSTRAP_REQUIRE_POPPER is deprcated : %s', __FILE__));

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
					'libFile' => \config::tempdir()  . '_bootstrap4_tmp.js'

				]);


			}

		}
		else {
			$root = realpath( __DIR__ . '/../../../../twbs');
			if ( !$root) {
				$root = realpath( __DIR__ . '/../../vendor/twbs');
			}

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
				\sys::logger( sprintf('<%s> %s', __DIR__, __METHOD__));
				throw new \Exception( 'Cannot locate twbs bootstrap - install with composer require twbs/bootstrap');

			}

		}

	}

	static public function serveFullcalendar( $type = 'css') {
		$root = realpath( __DIR__ . '/public/fullcalendar-4/');
		if ( $root) {
			if ( 'css' == $type) {
				$files = [
					$root . '/core/main.css',
					$root . '/bootstrap/main.css',

				];

				cssmin::viewcss([
					'debug' => false,
					'libName' => 'fullcalendar4',
					'cssFiles' => $files,
					'libFile' => \config::tempdir()  . '_fullcalendar4_tmp.css'

				]);

			}
			elseif ( 'js' == $type) {
				$path = realpath( sprintf( '%s/', $root));
				$files = [
					$root . '/core/main.js',
					$root . '/bootstrap/main.js',

				];

				jslib::viewjs([
					'debug' => false,
					'libName' => 'fullcalendar4',
					'jsFiles' => $files,
					'libFile' => \config::tempdir()  . '_fullcalendar4_tmp.js'

				]);

			}

		}
		else {
			throw new \Exception( 'Cannot locate fullcalendar-4 bootstrap');

		}

	}

}
