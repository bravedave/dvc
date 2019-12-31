<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * This work is licensed under a Creative Commons Attribution 4.0 International Public License.
 *      http://creativecommons.org/licenses/by/4.0/
 *
*/

namespace dvc;

abstract class url {
	static public $URL;
	static public $HOME;
	static public $PROTOCOL;

	static function tostring( string $url = '') {
		return ( sprintf( '%s%s', self::$URL, $url ));

	}

	static function write( string $url = '') {
		print self::swrite( $url);

	}

	static function swrite( string $url = '') {
		return ( self::tostring( $url));

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

			if ( !( defined( 'URL_APPLICATION' ))) define( 'URL_APPLICATION', '//' . $server . $script . '/' );

			if ( !( defined( 'URL' ))) {
				if ( application::use_full_url) {
					$script = preg_replace( '@/application$@', '', $script );
					define( 'URL', '//' . $server . $script . '/' );
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
