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

	static function tostring( $url = '') {
		return ( sprintf( '%s%s', self::$URL, $url ));

	}

	static function write( $url = '') {
		print self::swrite( $url);

	}

	static function swrite( $url = '') {
		return ( self::tostring( $url));

	}

	static function init() {
		if ( !isset( self::$URL )) {

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
				$ServerName = '';
				if ( isset( $_SERVER['SERVER_NAME'] ))
					$ServerName = strtolower( $_SERVER['SERVER_NAME'] );
				$ServerName = preg_replace( '@\/$@', '', $ServerName );

				$ScriptName = '';
				if ( isset( $_SERVER['SCRIPT_NAME'] ))
					$ScriptName = dirname( $_SERVER['SCRIPT_NAME'] );
				$ScriptName = preg_replace( '@(\/|\\\)$@', '', $ScriptName );

				if ( !( defined( 'URL_APPLICATION' )))
					define( 'URL_APPLICATION', '//' . $ServerName . $ScriptName . '/' );

				if ( !( defined( 'URL' ))) {
					if ( application::use_full_url) {
						$ScriptName = preg_replace( '@/application$@', '', $ScriptName );
						define( 'URL', '//' . $ServerName . $ScriptName . '/' );
						sys::logger( sprintf( 'defining URL as %s - %s', $ServerName, $ScriptName ), 3 );

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

			//~ sys::logger( 'url::init :: ' . URL );

		}

	}

}

url::init();
