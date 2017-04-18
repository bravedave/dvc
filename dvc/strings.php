<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/
NameSpace dvc;

abstract class strings {
	static function isValidMd5($md5 ='') {
	    return preg_match('/^[a-f0-9]{32}$/', $md5);

	}

	static function IsEmailAddress( $email) {
		//~ return ( filter_var($email, FILTER_VALIDATE_EMAIL));
		return ( self::CheckEmailAddress($email));

	}

	static function CheckEmailAddress( $email) {
		return ( filter_var($email, FILTER_VALIDATE_EMAIL));

	}

	static function isOurEmailDomain( $email) {
		$email_array = explode("@", $email);
		$domains = explode( ',', config::$EMAILDOMAIN);

		foreach ( $domains as $domain) {
			if ( strtolower( $email_array[1]) == trim( $domain))
				return ( TRUE);

		}

		return ( FALSE);

	}

	static function initials( $name ) {
		if ( (string)$name == "" )
			return '';

		$aX = explode( " ", $name );
		$xX = "";
		foreach ( $aX as $sX )
			$xX .= substr( $sX, 0, 1 );

		return ($xX );
	}

	static function lorum() {
		return html\element::lorum();

	}

	static function BRITISHDateAsANSI( $strDate) {
		// split it, must have 3 parts, dd/mm/yyyy
		$a = explode( "/", $strDate );
		if ( @checkdate( $a[1], $a[0], $a[2] ))
			return $a[2] . "-" . str_pad( $a[1], 2, "0", STR_PAD_LEFT ) . "-" . str_pad( $a[0], 2, "0", STR_PAD_LEFT );

		return 0;

	}

	static function InLocalTimeZone($format="r", $timestamp=false, $timezone=false) {
		$userTimezone = new \DateTimeZone( !empty($timezone) ? $timezone : 'GMT');
		$gmtTimezone = new \DateTimeZone( 'GMT');
		$myDateTime = new \DateTime( ( $timestamp != false ? date("r", (int)$timestamp) : date("r")), $gmtTimezone);
		$offset = $userTimezone->getOffset( $myDateTime);
		return date( $format, ($timestamp!=false ? (int)$timestamp : $myDateTime->format('U')) + $offset);

	}

	static function getDateAsANSI( $strDate) {

		if ( preg_match("@^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$@", $strDate))
			return ( date( 'Y-m-d', strtotime( $strDate)));

		if ( config::$DATE_FORMAT == 'd/m/Y' )
			return ( self::BRITISHDateAsANSI($strDate));


		throw new Exception( 'fix me:: strings::getDateAsANSI' );

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

	static function SmartCase($name) {
		$name = strtolower($name);
		$name = join("'", array_map('ucwords', explode("'", $name)));
		$name = join("-", array_map('ucwords', explode("-", $name)));
		$name = join("Mac", array_map('ucwords', explode("Mac", $name)));
		$name = join("Mc", array_map('ucwords', explode("Mc", $name)));
		return $name;

	}

	static function endswith($string, $test) {
		$strlen = strlen($string);
		$testlen = strlen($test);
		if ($testlen > $strlen) return false;
		return substr_compare( $string, $test, $strlen - $testlen, $testlen, TRUE) === 0;

	}

	static function array2csv(array &$array) {
		if (count($array) == 0)
			return null;

		ob_start();
		$df = fopen("php://output", 'w');
		//~ fputcsv( $df, array_keys( reset( $array)));
		foreach ( $array as $row)
			fputcsv($df, $row);

		fclose($df);

		return ob_get_clean();

	}

	static function htmlSanitize( $html ) {
		/*
			'@<style[^>]*?>.*?</style>@si',  	// Strip out javascript
			http://css-tricks.com/snippets/php/sanitize-database-inputs/
		*/

		$search = array(
			'@<head[^>]*?>.*?</head>@si',			// Strip head element
			'@<script[^>]*?>.*?</script>@si',		// Strip out javascript
			'@<!doctype[\/\!]*?[^<>]*?>@si',		// Strip doctype tags
			'@<(|/)html[^>]*?>@i',					// Strip <html> start and tag
			'@<(|/)body[^>]*?>@i',					// Strip <body> start and tag
			'@<link[^>]*?>.*?>@si',					// Strip link tags
			'@<base[\/\!]*?[^<>]*?>@si',			// Strip base href tags
			'@<style[^>]*?>.*?</style>@siU',		// Strip style tags
			'@<![\s\S]*?--[ \t\n\r]*>@',			// Strip multi-line comments including CDATA
			'@^<br[\s]/>@i'							// Blank HTML at Start
		);

		//~ '@(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n\']+@',	// Blank Lines at Start
		return( preg_replace($search, '', $html));

	}

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
			"/---\\n/",
			"/\\\\n/",
			"/\n/",
			"/\r/",
			"/$\*/"
		);

		$aR = array(
			'<hr align="left" style="width: 200px; margin: 0;" />',
			'<br />',
			'<br />',
			'<br />',
			'<br />&bull;'
		);

		if ( $allAsteriskAsList ) {
			$a[] = "/\*/";
			$aR[] = "<br />&bull;";
			$inText = rtrim( $inText, " .*" );
		}

		return ( preg_replace( $a, $aR, $inText));

	}

	static function html2text($document){
		$search = array(
			'@<[\/\!]*?[^<>]*?>@si',			// trim blank lines from beginning and end
			'@<br[\s]/>@si'
		);
		$text = preg_replace($search, '\n', self::htmlSanitize( $document));

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

}
