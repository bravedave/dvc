<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/

namespace dvc\ews\Exceptions;

class DirNotWritable extends Exception {
	protected $_text = 'DirNotWritable';

	public function __construct($message = null, $code = 0, Exception $previous = null) {
		if ( 0 !== strlen( $message))
			$this->_text .= sprintf( '%s is not writable', (string)$message);

		// make sure everything is assigned properly
		parent::__construct( $this->_text, $code, $previous);

	}

}
