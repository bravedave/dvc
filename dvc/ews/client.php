<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/
NameSpace dvc\ews;

use \jamesiarmes\PhpEws;

class client extends PhpEws\Client {

	static protected function _instance( Credentials $cred = NULL ) {
		if ( is_null( $cred))
			$cred = credentials::getCurrentUser();

		if ( $cred) {
			$client = new self(
				$cred->server,
				$cred->account,
				$cred->password,
				PhpEws\Client::VERSION_2010_SP2 );

			if ( isset( \config::$exchange_verifySSL) && !\config::$exchange_verifySSL) {
				$client->setCurlOptions([CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST  => false]);
				\sys::logger( 'ews\client :: disable verify SSL');

			}

			return ( $client);

		}

		return ( FALSE );

	}

	static function instance( Credentials $cred = NULL ) {
		if ( $client = self::_instance( $cred)) {
			if ( isset( \config::$exchange_timezone))
				$client->setTimezone( \config::$exchange_timezone);

		}

		return ( $client);

	}

	static function instanceForDelete( Credentials $cred = NULL ) {
		return ( self::_instance( $cred));

	}

	static function instanceForSync( Credentials $cred = NULL ) {
		return ( self::_instance( $cred));

	}

}
