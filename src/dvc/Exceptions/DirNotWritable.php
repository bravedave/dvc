<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc\Exceptions;

class DirNotWritable extends Exception {
	protected $_text = false;

	public function __construct($message = null, $code = 0, \Exception $previous = null) {

		if ( !$this->_text)
			$this->_text = '';

		if ( 0 !== strlen( $message))
			$this->_text .= sprintf( '%s is not writable', (string)$message);

		// make sure everything is assigned properly
		parent::__construct( $this->_text, $code, $previous);

	}

}
