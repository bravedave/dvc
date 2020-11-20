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
use config;
use libphonenumber;

abstract class strings {
	const html_tick = '&#10003;';
	const html_sad = '<span style="font-family: Segoe UI Symbol; Verdana;">&#9785;</span>';
	const html_happy = '<span style="font-family: Segoe UI Symbol; Verdana;">&#9786;</span>';

	static public function AMPM( $hhmm, $short = true, $tailed = true ) {
		$d = date( 'Y-m-d' ) . ' ' . $hhmm;
		if ( $short) {
			if ( date( 'i', strtotime( $d)) == '00') {
				return date( $tailed ? 'ga' : 'g:i', strtotime( $d));

			}
			else {
				return date( $tailed ? 'g:i a' : 'g:i', strtotime( $d));

			}

		}

		return date( $tailed ? 'h:i a' : 'h:i', strtotime( $d));

	}

	static function asLocalDate( $date, $time = false, $epoch = 0) {
		if ( (string)$date == '0000-00-00') {
			return ( false);

		}

		if ( ( $t = strtotime( $date)) > (float)$epoch) {
			// time should display if requested
			// if ( $time && date( 'Y-m-d', $t) == date( 'Y-m-d')) {
			if ( $time) {
				return ( preg_replace( '/m$/','',date( \config::$DATETIME_FORMAT, $t)));

			}
			else {
				return ( date( \config::$DATE_FORMAT, $t));

			}

		}

		return false;

	}

	static function asLocalPhone( $_tel = '' ) {
		$debug = false;
		// $debug = true;

		$_tel = preg_replace( '@[^0-9\+,]@', '', $_tel);
		if ( $_tel) {

			try {
				$phoneUtil = libphonenumber\PhoneNumberUtil::getInstance();
				if ( substr( $_tel, 0, 1) == '+') {
					$_mNo = $phoneUtil->parse( $_tel);
          if ( $debug) \sys::logger( sprintf( '<%s> : 1 : %s', $_mNo, __METHOD__));

				}
				else {
					$_mNo = $phoneUtil->parse( $_tel, \config::$PHONE_REGION);
					if ( $debug) \sys::logger( sprintf( '<%s> : 2 : %s', $_mNo, __METHOD__));

				}

        // if ( $debug) \sys::logger( sprintf( '<%s> %s', \config::$PHONE_REGION, __METHOD__));
				if ( $phoneUtil->isValidNumber( $_mNo, \config::$PHONE_REGION)) {
          if ( \config::$PHONE_REGION == $phoneUtil->getRegionCodeForNumber( $_mNo)) {
            if ( $debug) \sys::logger( sprintf( '<%s> : National : %s', $_mNo, __METHOD__));
						return $phoneUtil->format( $_mNo, libphonenumber\PhoneNumberFormat::NATIONAL);

					}
					else {
            if ( $debug) \sys::logger( sprintf( '<%s> : International : %s', $_mNo, __METHOD__));
						return $phoneUtil->format($_mNo, libphonenumber\PhoneNumberFormat::INTERNATIONAL);

					}

        }
        else {
          if ( $debug) \sys::logger( sprintf( '<%s> : Invalid : %s', $_mNo, __METHOD__));

        }


			}
			catch ( \Exception $e) {
				\sys::logger( sprintf( 'AsLocalPhoneA :: %s : %s', $_tel, $e->getMessage()));

			}

		}

		return ( $_tel);


	}

	static function asMobilePhone( $mobile = '' ) {
		//~ \sys::logger( sprintf( 'deprecated :: %s > use AsLocalPhone', __METHOD__));
		return self::AsLocalPhone( $mobile);

	}

	static function asShortDate( $date, $time = false) {
		if ( (string)$date == '0000-00-00') {
			return ( false);

		}

		if ( ( $t = strtotime( $date)) > 0) {
			if ( $time && date( 'Y-m-d', $t) == date( 'Y-m-d')) {
				return ( preg_replace( '/m$/','',date( 'g:ia', $t)));

			}
			elseif ( date( 'Y', $t) == date( 'Y')) {
				return ( date( 'd-M', $t));

			}
			else {
				return ( date( \config::$DATE_FORMAT, $t));

			}

		}

		return false;

	}

	static function asLongDate( $date, $time = false) {
		if ( (string)$date == '0000-00-00') {
			return ( false);

		}

		if ( ( $t = strtotime( $date)) > 0) {
			if ( $time) {
				return preg_replace(
          '/m$/',
          '',
          date( \config::$DATETIME_FORMAT_LONG, $t)

        );

			}
			else {
				return ( date( \config::$DATE_FORMAT_LONG, $t));

			}

		}

		return false;

	}

	static function array2csv(array &$array) {
		if (count($array) == 0) {
			return null;

		}

		ob_start();
		$df = fopen("php://output", 'w');
		//~ fputcsv( $df, array_keys( reset( $array)));
		foreach ( $array as $row) {
			fputcsv($df, $row);

		}

		fclose($df);

		$out = ob_get_contents();
		ob_end_clean();

		return $out;

	}

	static function BRITISHDateAsANSI( $strDate) {
		// split it, must have 3 parts, dd/mm/yyyy
		$a = explode( "/", $strDate );
		if ( @checkdate( $a[1], $a[0], $a[2] )) {
			if ( 2 == strlen( $a[2])) {
				$a[2] = substr( date('Y'), 0, 2) . $a[2];

			}
			return $a[2] . "-" . str_pad( $a[1], 2, "0", STR_PAD_LEFT ) . "-" . str_pad( $a[0], 2, "0", STR_PAD_LEFT );

		}

		return 0;

	}

	static function cleanPhoneString( string $tel) : string {
		//~ $debug = true;
		$debug = false;

		if ( strlen( $tel) > 8) {
			try {
				$phoneUtil = libphonenumber\PhoneNumberUtil::getInstance();
				if ( substr( $tel, 0, 1) == '+') {
					$_mNo = $phoneUtil->parse( $tel);
          if ( $debug) \sys::logger( sprintf('<%s> :1:%s', $_mNo, __METHOD__));

				}
				else {
					$_mNo = $phoneUtil->parse( $tel, \config::$PHONE_REGION);
          if ( $debug) \sys::logger( sprintf('<%s> :2:%s', $_mNo, __METHOD__));

				}

				if ( $phoneUtil->isValidNumber( $_mNo, \config::$PHONE_REGION)) {
          if ( $debug) \sys::logger( sprintf('<%s> :getRegionCodeForNumber:%s', $phoneUtil->getRegionCodeForNumber( $_mNo), __METHOD__));
          if ( 'AU' == $phoneUtil->getRegionCodeForNumber( $_mNo)) {
						$mNo = $phoneUtil->format($_mNo, libphonenumber\PhoneNumberFormat::NATIONAL);

					}
					else {
						$mNo = $phoneUtil->format($_mNo, libphonenumber\PhoneNumberFormat::INTERNATIONAL);

					}

          if ( $debug) \sys::logger( sprintf('<%s> :3:%s', $mNo, __METHOD__));
          return (string)preg_replace( '@[^0-9]@', '', $mNo);	// only numbers

				}
				else {
          return (string)preg_replace( '@[^0-9]@', '', $tel);	// only numbers

				}

			}
			catch ( libphonenumber\NumberParseException $e) {
        return (string)preg_replace( '@[^0-9]@', '', $tel);	// only numbers

			}

		}
		else {
			return (string)preg_replace( '@[^0-9]@', '', $tel);	// only numbers

		}

	}

	static function CheckEmailAddress( $email) {
		return ( filter_var($email, FILTER_VALIDATE_EMAIL));

	}

	static public function DateDiff( $date1, $date2 = null, $format = '%R%a') {
		if ( $date1 && '0000-00-00' != (string)$date1) {
			//~ \sys::logger( sprintf( '%s : %s', $date1));
			if ( !( strtotime( $date2) > 0)) $date2 = date( 'Y-m-d');

			$d1 = new \datetime( $date1);
			$d2 = new \datetime( $date2);
			$interval = date_diff($d1, $d2);
			//~ sys::logger( sprintf( '%s - %s = %s',  $date1, $date2, $interval->format('%R%a')));
			return ( $interval->format( $format));

		}

		return false;

	}

	static public function endswith($string, $test) {
		$strlen = strlen($string);
		$testlen = strlen($test);
		if ($testlen > $strlen) return false;
		return substr_compare( $string, $test, $strlen - $testlen, $testlen, TRUE) === 0;

	}

	static public function FirstNames( $string) {
		if ( preg_match( '/&/', $string)) {
			$x = explode( '&', $string);
			if ( count( $x) > 1)  {	// this might be david & lynne bray or david bray & lynne ralph
				//~ \sys::dump( $x);
				$a = [
					self::FirstWord( trim( $x[0])),
					self::FirstWord( trim( $x[1]))

				];

				return implode( ' & ', $a);

			} else { return self::FirstWord( $string); }

		} else { return self::FirstWord( $string); }

  }

	static public function FirstWord( $string) {
		return ( explode( ' ', trim( $string))[0]);

  }

	static public function formatBytes($bytes, $precision = 2) {
		$units = ['b', 'kb', 'mb', 'gb', 'tb'];

		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);

		$bytes /= pow(1024, $pow);

		return round($bytes, $precision) . ' ' . $units[$pow];

  }

	static public function getCommonPath( array $paths) : string {
		$lastOffset = 1;
		$common = '/';
		while (($index = strpos($paths[0], '/', $lastOffset)) !== FALSE) {
			$dirLen = $index - $lastOffset + 1;	// include /
			$dir = substr($paths[0], $lastOffset, $dirLen);
			foreach ($paths as $path) {
				if (substr($path, $lastOffset, $dirLen) != $dir)
				return $common;
			}
			$common .= $dir;
			$lastOffset = $index + 1;

		}
		return substr($common, 0, -1);

	}

	static public function getRelativePath( string $from, string $to, string $ps = DIRECTORY_SEPARATOR) : string {
		// https://www.php.net/manual/en/function.realpath.php#105876

		$arFrom = explode( $ps, rtrim( $from, $ps));
		$arTo = explode( $ps, rtrim( $to, $ps));
		while( count( $arFrom) && count( $arTo) && ( $arFrom[0] == $arTo[0])) {
			array_shift( $arFrom);
			array_shift( $arTo);

		}

		return str_pad("", count($arFrom) * 3, '..'.$ps).implode($ps, $arTo);

	}

	static public function getDateAsANSI( $strDate) {

		if ( preg_match("@^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$@", $strDate))
		return ( date( 'Y-m-d', strtotime( $strDate)));

		if ( config::$DATE_FORMAT == 'd/m/Y' )
		return ( self::BRITISHDateAsANSI($strDate));


		throw new \Exception( 'fix me:: strings::getDateAsANSI' );

	}

	static public function getGUID() {
		return ( sprintf( '{%s}', self::getUID()));

	}

	static public function getUID(){
		$charid = strtoupper(md5(self::rand()));
		$uuid = sprintf( '%s-%s-%s-%s-%s',
			substr($charid, 0, 8),
			substr($charid, 8, 4),
			substr($charid,12, 4),
			substr($charid,16, 4),
			substr($charid,20,12));

		return $uuid;

	}

	static public function GoodStreetString( $street ) {
		if ( preg_match( '/The\s?Drive/i', $street ))
			return ( $street);
		if ( preg_match( '/The\s?Avenue/i', $street ))
			return ( $street);

		$find = [
			'@\sroad$@i','@\sroad,@i',
			'@\sstreet$@i','@\sstreet,@i','@\sstreet\s@i',
			'@\savenue$@i','@\savenue,@i','@\save$@i',
			'@\sparade$@i','@\spde$@i','@\sparade,@i','@\spde,@i',
			'@\sterrace$@i','@\stce$@i','@\sterrace,@i','@\stce,@i',
			'@\sdrive$@i','@\sdrive,@i',
			'@\splace$@i','@\splace,@i',
			'@\scourt$@i','@\scourt,@i',
			'@\screscent$@i','@\screscent,@i'
			];
		$replace = [
			' Rd',' Rd,',
			' St',' St,',' St, ',
			' Av',' Av,',' Av,',
			' Pd',' Pd',' Pd,',' Pd,',
			' Tc',' Tc',' Tc,',' Tc,',
			' Dr',' Dr,',
			' Pl',' Pl,',
			' Ct',' Ct,',
			' Cres',' Cres,'
			];


		return ( trim( preg_replace( $find, $replace, $street ), ', '));

  }

	static public function HoursMinutes( $str) {
		$hm = self::HoursMinutesSeconds( $str);
		return (
			str_pad( (string)$hm["hours"], 2, "0", STR_PAD_LEFT ) . ":" .
			str_pad( (string)$hm["minutes"], 2, "0", STR_PAD_LEFT ));

	}

	static public function HoursMinutesSeconds( $str, $format = 'array' ) {
		$str = trim( $str );
		if ($str == '') return 0;

		$iOffset = 0;
		$eTag = substr( $str, strlen( $str )-1, 1 );
		$iHours = 0;
		$iMinutes = 0;
		$iSeconds = 0;

		if ( $eTag == 'p' || $eTag == 'P' ) {
			$iOffset = 12;
			$str = trim( substr( $str, 0, strlen( $str ) -1 ));

    }
    elseif ( $eTag == 'a' || $eTag == 'A' ) {
			$str = trim( substr( $str, 0, strlen( $str ) -1 ));

    }
    elseif ( $eTag == 'm' || $eTag == 'M' ) {
			$eTag = strtolower( substr( $str, strlen( $str )-2, 2 ));
			if ( $eTag == 'pm' ) {
				$iOffset = 12;
				$str = trim( substr( $str, 0, strlen( $str ) -2 ));

      }
      elseif ( $eTag == 'am' ) {
				$str = trim( substr( $str, 0, strlen( $str ) -2 ));

      }

		}

		//echo "Lookin @:$str\n";
		if ( is_integer(strpos( $str, ':'))) {
			// hours and minutes
			$a = explode( ":", $str);
			//print_r($a);
			$iHours = (int)$a[0];
			$iMinutes = (int)$a[1];

    }
    elseif ( strpos( $str, '.') > 0 ) {
			// hours and minutes
			$a = explode( ".", $str);
			$iHours = (int)$a[0];
			$iMinutes = (int)$a[1];

    }
    else {
			$iHours = (int)$str;
			//echo "Nuffin:$str\n";

		}

		$aRet = [
			"hours" => ( $iHours + ( $iHours == 12 ? 0 : $iOffset )),
			"minutes" => $iMinutes,
			"seconds" => $iSeconds

		];

		if ( $format == "string" ) {
			return (
				str_pad( (string)$aRet["hours"], 2, "0", STR_PAD_LEFT ) . ":" .
				str_pad( (string)$aRet["minutes"], 2, "0", STR_PAD_LEFT ) . ":" .
				str_pad( (string)$aRet["seconds"], 2, "0", STR_PAD_LEFT ) );

		}
		else {
			return ($aRet);

		}

  }

	static function html2text($document) : string {
		$search = array(
			'@<[\/\!]*?[^<>]*?>@si',			// trim blank lines from beginning and end
			'@<br[\s]/>@si',
			'@&nbsp;@si',
      '@&amp;@si'
		);
		$replace = array(
			PHP_EOL,
			PHP_EOL,
			' ',
      '&'
    );

    $text = self::htmlSanitize( $document);
    // die( $text);
    $text = preg_replace_callback( '/<li[^>]*>([^<]*)<\\/li>/i', function( $s) {
      // \sys::logger( sprintf('<%s> %s', print_r( $s, true), __METHOD__));
      return '- ' . $s[1];

    }, $text);
    // die( $text);

		$text = preg_replace( [
      "@<ul[^>]*>\n@",
      "@</ul[^>]*>\n@",
      "@</p[^>]*>@"
    ], "", $text );
    $text = trim( preg_replace($search, $replace, $text), '\n');
    // die( $text);

		/*
		$search = array('@<script[^>]*?>.*?</script>@si',	// Strip out javascript
						'@<style[^>]*?>.*?</style>@siU',	// Strip style tags properly
						'@<![\s\S]*?--[ \t\n\r]*>@',		// Strip multi-line comments including CDATA
						'@<[\/\!]*?[^<>]*?>@si'				// trim blank lines from beginning and end
		);

		$text = preg_replace($search, '', $text);
		*/
		//~ $text = $document;

		$x = preg_split( "/\n/", $text );
		while ( count($x) > 0 && trim( $x[0] ) == "" )
			array_shift( $x );

		while ( count($x) > 0 && trim( $x[(count($x)-1)] ) == "" )
			array_pop( $x );

		$text = implode( "\n", $x );

		return $text;

	}

	static function htmlSanitize( $html ) {
		/*
			'@<style[^>]*?>.*?</style>@si',  	// Strip out javascript
			http://css-tricks.com/snippets/php/sanitize-database-inputs/
		*/

		$search = [];
        $replace = [];

        $search[] = '@<head[^>]*?>.*?</head>@si';			// Strip head element
        $replace[] = '';

        $search[] = '@<script[^>]*?>.*?</script>@si';		// Strip out javascript
        $replace[] = '';

        $search[] = '@<!doctype[\/\!]*?[^<>]*?>@si';		// Strip doctype tags
        $replace[] = '';

        $search[] = '@<(|/)html[^>]*?>@i';					// Strip <html> start/end tag
        $replace[] = '';

        $search[] = '@<body([^>]*)>@i';					    // mod <body> start tag
        $replace[] = '<div data-x_type="body" ${1}>';

        $search[] = '@</body[^>]*?>@i';					    // mod <body> end tag
        $replace[] = '</div>';

        $search[] = '@<link[^>]*?>@si'; 					// Strip link tags
        $replace[] = '';

        $search[] = '@<base[\/\!]*?[^<>]*?>@si';			// Strip base href tags
        $replace[] = '';

        $search[] = '@<style[^>]*?>.*?</style>@si';			// Strip style tags
        $replace[] = '';

        $search[] = '@<![\s\S]*?--[ \t\n\r]*>@';			// Strip multi-line comments including CDATA
        $replace[] = '';

        $search[] = '@^<br[\s]/>@i';						// Blank HTML at Start
        $replace[] = '';

		//~ '@(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n\']+@',	// Blank Lines at Start
		return( preg_replace($search, $replace, $html));

  }

  static function imageInline( $path) {
    $data = base64_encode( file_get_contents( $path));
    return 'data:'.mime_content_type($path).';base64,'.$data;

  }

	static function initials( $name ) {
		if ( (string)$name == "" )
		return '';

		$aX = explode( " ", $name );
		$xX = "";
		foreach ( $aX as $sX ) {
			$xX .= substr( $sX, 0, 1 );

		}

		return ($xX );

	}

	static function InLocalTimeZone($format="r", $timestamp=false, $timezone=false) {
		$userTimezone = new \DateTimeZone( !empty($timezone) ? $timezone : 'GMT');
		$gmtTimezone = new \DateTimeZone( 'GMT');
		$myDateTime = new \DateTime( ( $timestamp != false ? date("r", (int)$timestamp) : date("r")), $gmtTimezone);
		$offset = $userTimezone->getOffset( $myDateTime);
		return date( $format, ($timestamp!=false ? (int)$timestamp : $myDateTime->format('U')) + $offset);

	}

	static function isValidMd5($md5 ='') {
		return preg_match('/^[a-f0-9]{32}$/', $md5);

	}

	static function IsEmailAddress( $email) {
		return ( self::CheckEmailAddress($email));

	}

	static function isEmail( $email) {	// compatible case and naming with my javascript routine
		return ( self::CheckEmailAddress($email));

	}

	static function isMobilePhone( string $_tel = '') : bool {
		try {
			$tel = preg_replace( '@[^0-9\+]@','', $_tel);
			//~ \sys::logger( sprintf( 'IsMobilePhone :: %s', $tel));

			if ( $tel && \strlen( $tel) >= 10 && \strlen( $tel) < 17) {
				/**
				 * to prove
				 * a mobile phone must contain 10 numbers
				 */
				$phoneNumberUtil = libphonenumber\PhoneNumberUtil::getInstance();

				$phoneNumberObject = ( '+' == substr( $tel, 0, 1) ? $phoneNumberUtil->parse($tel) : $phoneNumberUtil->parse($tel, 'AU'));

				$numberType = $phoneNumberUtil->getNumberType( $phoneNumberObject);

				if ( $numberType == libphonenumber\PhoneNumberType::MOBILE) {
					return ( true);

				}

			}

		}
		catch ( \Exception $e) {
			\sys::logger( sprintf( '%s : %s : %s', $_tel, $e->getMessage(), __METHOD__));

		}

		return ( false);

	}

	static function isPhone( string $_tel = '') : bool {
		try {
			$tel = preg_replace( '@[^0-9\+]@','', $_tel);
			//~ \sys::logger( sprintf( 'IsMobilePhone :: %s', $tel));

			if ( $tel && \strlen( $tel) >= 10 && \strlen( $tel) < 17) {
				/**
				 * to prove
				 * a mobile phone must contain 10 numbers
				 */
				$phoneNumberUtil = libphonenumber\PhoneNumberUtil::getInstance();

				$phoneNumberObject = ( '+' == substr( $tel, 0, 1) ? $phoneNumberUtil->parse($tel) : $phoneNumberUtil->parse($tel, 'AU'));

				return (bool)$phoneNumberUtil->isValidNumber( $phoneNumberObject, \config::$PHONE_REGION);

			}

		}
		catch ( \Exception $e) {
			\sys::logger( sprintf( '%s : %s : %s', $_tel, $e->getMessage(), __METHOD__));

		}

		return ( false);

	}

	static function isOurEmailDomain( $email) {
		$email_array = explode("@", $email);
		$domains = explode( ',', config::$EMAILDOMAIN);

		foreach ( $domains as $domain) {
			if ( strtolower( $email_array[1]) == trim( $domain))
				return ( true);

		}

		return ( false);

	}

	static function lorum() {
		return html\element::lorum();

	}

	protected static $_pixel = false;
	static function pixel() {
		if ( !self::$_pixel) {
			$image = implode( DIRECTORY_SEPARATOR, [
				__DIR__,
				'public',
				'images',
				'pixel.png'

			]);
			// $imageData = base64_encode( file_get_contents($image));
			// self::$_pixel = 'data:'.mime_content_type($image).';base64,'.$imageData;
			self::$_pixel = self::imageInline( $image);

		}

		return self::$_pixel;

	}

	static function rand( $prefix = 'uid_') {
		return ( $prefix . bin2hex( random_bytes( 11)));

	}

	static function replaceWordCharacters( $text) {

		// Replaces commonly-used Windows 1252 encoded chars
		// that do not exist in ASCII or ISO-8859-1 with
		// ISO-8859-1 cognates.
		$str = $text;

		$s = [];
		$r = [];

		// smart single quotes and apostrophe
		//~ $s[] = sprintf( '@(\x{2018}|\x{2019}|\x{201A})@');
		$s[] = sprintf( '@(%s|%s|%s)@', "\u{2018}", "\u{2019}", "\u{201A}");
		// sys::logger( $s[0]);

		$r[] = "'";

		// smart double quotes
		$s[] = sprintf( '@(%s|%s|%s)@', "\u{201C}", "\u{201D}", "\u{201E}");
		$r[] = '"';

		/**
		 * * ellipsis
		 * s = s.replace(/\u2026/g, "...");
		 *
		 * * dashes
		 * s = s.replace(/[\u2013|\u2014]/g, "-");
		 *
		 * * circumflex
		 * s = s.replace(/\u02C6/g, "^");
		 *
		 * * open angle bracket
		 * s = s.replace(/\u2039/g, "<");
		 *
		 * * close angle bracket
		 * s = s.replace(/\u203A/g, ">");
		 *
		 * * spaces
		 * s = s.replace(/[\u02DC|\u00A0]/g, " ");
		 */

		return preg_replace( $s, $r, $str);

	}

	static function SmartCase($name) {
		$name = strtolower($name);
		$name = join("'", array_map('ucwords', explode("'", $name)));
		$name = join("-", array_map('ucwords', explode("-", $name)));
		$name = join("Mac", array_map('ucwords', explode("Mac", $name)));
		$name = join("Mc", array_map('ucwords', explode("Mc", $name)));
		return $name;

	}

	static function street_index( $street ) {
    /**
     * Makes a street no, name into an indexable value
     * 38 Chapel Street becomes
     * Chapel Street   38
     */
		$strStreetIndex = $street;	// safety

		if ( is_numeric( substr( $street, 0, 1 ) ) ) {
			$aStreet = explode( ' ', $street );

			$no = array_shift( $aStreet);
			if ( false != strpos( $no, '/')) {
				$_no = explode('/', $no);
				$_r = array_reverse( $_no);
				$no = implode( ' ', $_r);

			}

			$aStreet[] = str_pad( trim( (string)$no), 6, ' ', STR_PAD_LEFT);
			$strStreetIndex = implode( ' ', $aStreet );
			if ( '' == $strStreetIndex)
				$strStreetIndex = $street;	// safety

		}

		return ( $strStreetIndex);

  }

  static function text2html( $inText, $maxrows = -1, $allAsteriskAsList = false ) {

		if ( $maxrows > 0 ) {
			$x = preg_split( "/\n/", $inText );
			while ( count( $x ) > ($maxrows+1) )
				array_pop( $x );
			$inText = implode( "<br />", $x );

		}

		$a = [
			"/---\\n/",
			"/\\\\n/",
			"/\r\n/",
			"/\n/",
			"/\r/",
			"/$\*/",
			"/\s\s/"
		];

		$aR = array(
			'<hr align="left" style="width: 200px; margin: 0;" />',
			'<br />',
			'<br />',
			'<br />',
			'<br />',
			'<br />&bull;',
			'&nbsp;&nbsp;'
		);

		if ( $allAsteriskAsList ) {
			$a[] = "/\*/";
			$aR[] = "<br />&bull;";
			$inText = rtrim( $inText, " .*" );
		}

		return ( preg_replace( $a, $aR, $inText));

	}

	static function url( string $url = '', bool $protocol = false) : string {
		return url::toString( $url, $protocol);

	}

	static function xml_entities($text, $charset = 'UTF-8'){
		 // Debug and Test
		// $text = "test &amp; &trade; &amp;trade; abc &reg; &amp;reg; &#45;";

		/*
			First we encode html characters that are also invalid in xml
			*/
		$text = htmlentities($text, ENT_COMPAT, $charset, false);

		/*
			XML character entity array from Wiki
			Note: &apos; is useless in UTF-8 or in UTF-16
			*/
		$arr_xml_special_char = array("&quot;","&amp;","&apos;","&lt;","&gt;");

		/*
			Building the regex string to exclude all strings with xml special char
			*/
		$arr_xml_special_char_regex = "(?";
		foreach($arr_xml_special_char as $key => $value){
			$arr_xml_special_char_regex .= "(?!$value)";
		}
		$arr_xml_special_char_regex .= ")";

		/*
			Scan the array for &something_not_xml; syntax
			*/
		$pattern = "/$arr_xml_special_char_regex&([a-zA-Z0-9]+;)/";

		/*
			Replace the &something_not_xml; with &amp;something_not_xml;
			*/
		$replacement = '&amp;${1}';
		return preg_replace($pattern, $replacement, $text);

	}

}
