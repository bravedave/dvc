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
NameSpace dvc;

abstract class bCrypt {
	static function crypt( $input ) {

		$cipher = mcrypt_module_open(MCRYPT_TRIPLEDES, '', 'ecb', '');
		mcrypt_generic_init( $cipher, \config::$CRYPT_KEY, \config::$CRYPT_IV );

		$encrypted_data = mcrypt_generic( $cipher, $input );

		mcrypt_generic_deinit( $cipher );
		mcrypt_module_close( $cipher );

		return base64_encode( $encrypted_data);

	}

	static function decrypt( $encrypted_text ) {

		$cipher = mcrypt_module_open( MCRYPT_TRIPLEDES, '', 'ecb', '');

		mcrypt_generic_init($cipher, \config::$CRYPT_KEY, \config::$CRYPT_IV);

		$decrypted_data = mdecrypt_generic( $cipher, base64_decode($encrypted_text));

		mcrypt_generic_deinit( $cipher);
		mcrypt_module_close( $cipher);
		return $decrypted_data;

	}

}
