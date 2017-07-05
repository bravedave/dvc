<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/

Namespace dvc;

abstract class gravatar {
	static function hash( $email ) {
		return ( md5( strtolower( trim( $email ))));
	}

	static function url( $email ) {
		return ( sprintf( '//www.gravatar.com/avatar/%s?d=mm', self::hash( $email )));
	}

	static function profile( $email ) {
		$url = sprintf( 'http://www.gravatar.com/%s.php', self::hash( $email));

		$http = new HttpGet( $url);
		curl_setopt( $http->ch, CURLOPT_FOLLOWLOCATION, true );

		if ( $http->send()) {
			/* the response is in the form
				Indicator - either:
					a - array
					s - string
				Number
					for arrays 1 perhaps the number or profiles returned
					for strings the ength of the response
				Response
					for arrays the profile
					for strings text
					*/


			$profile = unserialize( $http->getResponse());
			if ( is_array( $profile ) && isset( $profile['entry'] ) ) {
				$profile = $profile['entry'][0];
				return ( (object)$profile);

			}

		}

		return ( FALSE);

	}

	static function icon( $email, $class = 'gravatar gravatar-80' ) {
		return ( sprintf( '<img src="%s" class="%s" />', self::url( $email ), $class));
	}

}