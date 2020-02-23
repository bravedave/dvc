<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/
namespace dvc;

abstract class userAgent {
	protected static $useragent;

	static function html5Compliant() {
		// global $isAndroid, $isIPhone, $isIPad, $isIE, $isIE10, $isChrome, $isSafari, $isIPhoneWebApp, $isMobileDevice;
		// global $isChrome;
		if ( self::isChrome()) {
			return ( true );

		}

		if ( self::isIE()) {
			if(preg_match('/(?i)msie [1-8]/',$_SERVER['HTTP_USER_AGENT'])) {
				return ( false );

			}

			\sys::logger( $_SERVER['HTTP_USER_AGENT']);

		}

		return ( true );

	}

	static function isAndroid() {
		return ( preg_match('/Android/', self::$useragent));

	}

	static function isBlackberry() {
		return ( preg_match('/BlackBerry/', self::$useragent));

	}

	static function isChrome() {
		return ( preg_match('/Chrome/', self::$useragent));

	}

	static function isChromeOnIOS() {
		if ( self::isIPhone())
			return ( preg_match('/CriOS/', self::$useragent));

		return ( false);

	}

	static function isEdge() {
		return ( preg_match('/Edge/', self::$useragent));

	}

	static function isFirefox() {
		return ( preg_match('/Gecko/', self::$useragent));

	}

	static function isGoogleBot() {
		// HTTP_USER_AGENT => Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)
		if( preg_match('/Googlebot/', self::$useragent))
			return ( true);

		return ( false );

	}

	static function isIE() {
		return ( preg_match('/MSIE|Trident/', self::$useragent));

	}

	static function isIPad() {
		return ( preg_match('/iPad/', self::$useragent));

	}

	static function isIPhone() {
		return ( preg_match('/iPhone|iPod/', self::$useragent));

	}

	static function isIPhoneWebApp() {
		return ( self::isSafari() && self::isIPhone());

	}

	static function isLegacyIE() {
		if( preg_match('/(?i)msie [5-8]/', self::$useragent))
			return ( true);

		return ( false );

	}

	static function isLegit() {

		if ( self::isChrome()) {
			if ( self::version() > 60) {
				// sys::logger( sprintf( 'Valid Chrome: %s : %s', self::version(), self::$useragent));
				return ( true);

			}

			sys::logger( sprintf( 'Invalid Chrome: %s : %s', self::version(), self::$useragent));
			return ( false);

		}
		elseif ( self::isFirefox()) {
			if ( self::version() < 50) {
				/* this is probably nightly */
				$v = preg_replace( '@^.*Firefox\/@', '', self::$useragent);
 		 		if ( (float)$v > 59) {
					return ( true);

				}

				sys::logger( sprintf( 'query Firefox: %s : %s : %s', (float)$v, $v, self::$useragent));

			}

			if ( self::version() > 50) {
				// sys::logger( sprintf( 'Valid Firefox: %s : %s', self::version(), self::$useragent));
				return ( true);

			}

			sys::logger( sprintf( 'Invalid Firefox: %s : %s', self::version(), self::$useragent));
			return ( false);

		}

		sys::logger( self::$useragent);

		if ( self::isSafari())
			return ( true);

		if ( self::isEdge())
			return ( true);

		if ( self::isIE())
			return ( true);

	}

	static function isMobileDevice() {
		return ( self::isIPhone() || self::isBlackberry() || self::isAndroid() || self::isIPad());

	}

	static function isSafari() {
		return ( preg_match('/Safari/', self::$useragent));

	}

	static function os() {
		$os_array = [
				'/windows nt 10/i'     =>  'Windows 10',
				'/windows nt 6.3/i'     =>  'Windows 8.1',
				'/windows nt 6.2/i'     =>  'Windows 8',
				'/windows nt 6.1/i'     =>  'Windows 7',
				'/windows nt 6.0/i'     =>  'Windows Vista',
				'/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
				'/windows nt 5.1/i'     =>  'Windows XP',
				'/windows xp/i'         =>  'Windows XP',
				'/windows nt 5.0/i'     =>  'Windows 2000',
				'/windows me/i'         =>  'Windows ME',
				'/win98/i'              =>  'Windows 98',
				'/win95/i'              =>  'Windows 95',
				'/win16/i'              =>  'Windows 3.11',
				'/macintosh|mac os x/i' =>  'Mac OS X',
				'/mac_powerpc/i'        =>  'Mac OS 9',
				'/linux/i'              =>  'Linux',
				'/ubuntu/i'             =>  'Ubuntu',
				'/iphone/i'             =>  'iPhone',
				'/ipod/i'               =>  'iPod',
				'/ipad/i'               =>  'iPad',
				'/android/i'            =>  'Android',
				'/blackberry/i'         =>  'BlackBerry',
				'/webos/i'              =>  'Mobile' ];

		foreach ($os_array as $regex => $value) {
			if ( preg_match($regex, self::$useragent))
				return ( $value);

		}

		return ( sprintf( 'Unknown OS Platform (%s)', self::$useragent));

	}

	static function version() {
		if ( ini_get('browscap')) {
			/*
			 * get latest version of browscap.ini here: http://browscap.org/
			 */
			$browser = get_browser( null, false);
			// sys::dump( $browser);
			if ( isset( $browser->version))
				return ( $browser->version);

		}

		return ( 0);

	}

	static function init() {
		if ( !isset( self::$useragent )) {
			self::$useragent = '';
			if ( isset( $_SERVER["HTTP_USER_AGENT"]))
				self::$useragent = $_SERVER['HTTP_USER_AGENT'];

			//~ sys::logger( self::$useragent);

		}

	}

	static function toString() {
		return self::$useragent;

	}

}

userAgent::init();
