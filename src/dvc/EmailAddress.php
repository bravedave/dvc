<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 *  DO NOT change this file
 *  Copy it to <application>/app/dvc/ and modify it there
*/

namespace dvc;

class EmailAddress {
	var $email, $name;

	function __construct( $el ) {
		if ( strpos( $el, '<' ) !== false ) {
			$el = trim( $el, '> ' );
			$a = explode( "<", $el );

			/* remove quote enclosures */
			$this->name = preg_replace(
				[
					'/^("|\')/',
					'/("|\')$/'
				],
				'', trim( $a[0] ));

			$this->email = trim( $a[1] );

		}
		else {
			$this->name = '';
			$this->email = trim( $el);

		}

	}

	function check() {
		return ( \strings::CheckEmailAddress( $this->email));

	}

}
