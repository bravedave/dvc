<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	DO NOT change this file
	Copy it to <application>/app/dvc/ and modify it there
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
