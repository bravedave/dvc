<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/
NameSpace dvc;

abstract class strings extends utility {
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

	static public function getCommonPath( $paths) {
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

}
