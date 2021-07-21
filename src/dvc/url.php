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

abstract class url {
	static public $URL;
	static public $HOME;
	static public $PROTOCOL;

	static function tostring( string $url = '', bool $protocol = false) : string {
		if ( $protocol) {
			return ( implode( [ self::$PROTOCOL, self::$URL, $url]));

		}
		else {
			return ( implode([ self::$URL, $url]));

		}

	}

	static function write( string $url = '', bool $protocol = false) {
		print self::tostring( $url, $protocol);

	}

	static function swrite( string $url = '', bool $protocol = false) : string {
		return ( self::tostring( $url, $protocol));

	}

	static function init() {
		if ( isset( self::$URL )) return;

		if ( !( defined( 'URL' ))) {
			if ( isset( $_SERVER['SERVER_SOFTWARE'] )) {
				if ( preg_match( '@^PHP@', $_SERVER['SERVER_SOFTWARE'] )) {
					if ( application::use_full_url) {
						if ( isset( $_SERVER['SERVER_PORT'] ) && $_SERVER['SERVER_PORT'] != 80)
							define( 'URL', sprintf( '//localhost:%s/', $_SERVER['SERVER_PORT']));
						else
							define( 'URL', '//localhost/' );

					}
					else {
						define( 'URL', '/' );

					}

				}

			}

		}

		if ( !( defined( 'URL' ) && defined( 'URL_APPLICATION' ) ) ) {
			$server = '';
			if ( isset( $_SERVER['SERVER_NAME'] )) $server = strtolower( $_SERVER['SERVER_NAME'] );
			$server = preg_replace( '@\/$@', '', $server );

			$script = '';
			if ( isset( $_SERVER['SCRIPT_NAME'] )) $script = dirname( $_SERVER['SCRIPT_NAME'] );
			$script = preg_replace( '@(\/|\\\)$@', '', $script );

      $port = '';
      if (preg_match('@^Apache@', $_SERVER['SERVER_SOFTWARE']??'')) {
        if (application::use_full_url) {
          if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != 80) {
            // \sys::logger(sprintf('<%s:%s> %s', $_SERVER['SERVER_SOFTWARE'], $_SERVER['SERVER_PORT'], __METHOD__));
            $port = ':' . $_SERVER['SERVER_PORT'];
          }
        }
      }

      if ( !( defined( 'URL_APPLICATION' ))) define( 'URL_APPLICATION', '//' . $server . $script . $port . '/' );

			if ( !( defined( 'URL' ))) {
				if ( application::use_full_url) {
					$script = preg_replace( '@/application$@', '', $script );
					define( 'URL', '//' . $server . $script . $port . '/' );
					// \sys::logger( sprintf( 'defining URL as %s - %s', $server, $script ), 3 );

				}
				else {
					define( 'URL', '/' );

				}

			}

		}

		self::$URL = URL;
		self::$HOME = URL;

		$protocol = ( ! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ||
			( isset( $_SERVER['SERVER_PORT'] ) && $_SERVER['SERVER_PORT'] == 443 )) ? "https:" : "http:";

		self::$PROTOCOL = $protocol;

	}

}

url::init();
