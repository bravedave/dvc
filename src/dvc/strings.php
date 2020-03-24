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

abstract class strings {
	const html_tick = '&#10003;';
	const html_sad = '<span style="font-family: Segoe UI Symbol; Verdana;">&#9785;</span>';
	const html_happy = '<span style="font-family: Segoe UI Symbol; Verdana;">&#9786;</span>';

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
		//~ $debug = true;

		$_tel = preg_replace( '@[^0-9\+,]@', '', $_tel);
		if ( $_tel) {

			try {
				$phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
				if ( substr( $_tel, 0, 1) == '+') {
					$_mNo = $phoneUtil->parse( $_tel);
					if ( $debug) \sys::logger( sprintf( 'phoneformats::AsLocalPhone:1: %s', $_mNo));

				}
				else {
					$_mNo = $phoneUtil->parse( $_tel, \config::$PHONE_REGION);
					if ( $debug) \sys::logger( sprintf( 'phoneformats::AsLocalPhone:2: %s', $_mNo));

				}

				if ( $phoneUtil->isValidNumber( $_mNo)) {
					if ( $phoneUtil->getRegionCodeForNumber( $_mNo) == 'AU') {
						return $phoneUtil->format($_mNo, \libphonenumber\PhoneNumberFormat::NATIONAL);

					}
					else {
						return $phoneUtil->format($_mNo, \libphonenumber\PhoneNumberFormat::INTERNATIONAL);

					}


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

	static function endswith($string, $test) {
		$strlen = strlen($string);
		$testlen = strlen($test);
		if ($testlen > $strlen) return false;
		return substr_compare( $string, $test, $strlen - $testlen, $testlen, TRUE) === 0;

	}

	static function getCommonPath( $paths) {
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

	static function getDateAsANSI( $strDate) {

		if ( preg_match("@^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$@", $strDate))
		return ( date( 'Y-m-d', strtotime( $strDate)));

		if ( config::$DATE_FORMAT == 'd/m/Y' )
		return ( self::BRITISHDateAsANSI($strDate));


		throw new \Exception( 'fix me:: strings::getDateAsANSI' );

	}

	static function getGUID() {
		return ( sprintf( '{%s}', self::getUID()));

	}

	static function getUID(){
		$charid = strtoupper(md5(self::rand()));
		$uuid = sprintf( '%s-%s-%s-%s-%s',
			substr($charid, 0, 8),
			substr($charid, 8, 4),
			substr($charid,12, 4),
			substr($charid,16, 4),
			substr($charid,20,12));

		return $uuid;

	}

	static function html2text($document){
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
		$text = trim( preg_replace($search, $replace, self::htmlSanitize( $document)), '\n');

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

		$search = [
			'@<head[^>]*?>.*?</head>@si',			// Strip head element
			'@<script[^>]*?>.*?</script>@si',		// Strip out javascript
			'@<!doctype[\/\!]*?[^<>]*?>@si',		// Strip doctype tags
			'@<(|/)html[^>]*?>@i',					// Strip <html> start and tag
			'@<(|/)body[^>]*?>@i',					// Strip <body> start and tag
			'@<link[^>]*?>@si',						// Strip link tags
			'@<base[\/\!]*?[^<>]*?>@si',			// Strip base href tags
			'@<style[^>]*?>.*?</style>@si',			// Strip style tags
			'@<![\s\S]*?--[ \t\n\r]*>@',			// Strip multi-line comments including CDATA
			'@^<br[\s]/>@i'							// Blank HTML at Start
		];

		//~ '@(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n\']+@',	// Blank Lines at Start
		return( preg_replace($search, '', $html));

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
				$phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();

				$phoneNumberObject = ( '+' == substr( $tel, 0, 1) ? $phoneNumberUtil->parse($tel) : $phoneNumberUtil->parse($tel, 'AU'));

				$numberType = $phoneNumberUtil->getNumberType( $phoneNumberObject);

				if ( $numberType == \libphonenumber\PhoneNumberType::MOBILE) {
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
				$phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();

				$phoneNumberObject = ( '+' == substr( $tel, 0, 1) ? $phoneNumberUtil->parse($tel) : $phoneNumberUtil->parse($tel, 'AU'));

				return (bool)$phoneNumberUtil->isValidNumber( $phoneNumberObject);

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
			$imageData = base64_encode( file_get_contents($image));
			self::$_pixel = 'data:'.mime_content_type($image).';base64,'.$imageData;

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
