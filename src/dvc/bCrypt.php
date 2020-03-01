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

abstract class bCrypt {
	static function crypt( $input ) {
		return base64_encode( openssl_encrypt( $input, 'bf-cbc', \config::$CRYPT_KEY, 0, \config::$CRYPT_IV ));

	}

	static function decrypt( $encrypted_text ) {
		return openssl_decrypt( base64_decode( $encrypted_text), 'bf-cbc', \config::$CRYPT_KEY, 0, \config::$CRYPT_IV );

	}

}
