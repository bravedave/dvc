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

	static function instance( Credentials $cred = NULL ) {
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

			if ( isset( \config::$exchange_timezone))
				$client->setTimezone( \config::$exchange_timezone);

			return ( $client);

		}

		return ( FALSE );

	}

}
