<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/
namespace dvc\Exceptions;

class DatapathNotWritable extends Exception {
	protected $_text = 'error/datapath not writable';

	public function __construct($message = null, $code = 0, \Exception $previous = null) {

		$this->_text .= implode( '<br />', [
            sprintf( 'please create a writable data folder : %s', $message),
            sprintf( 'mkdir --mode=0777 %s', $message ),

        ]);

		// make sure everything is assigned properly
		parent::__construct( $this->_text, $code, $previous);

	}

}
