<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	Inspired by MailSo;

	*/
NameSpace dvc\Exceptions;

class Exception extends \Exception {
	protected $_text = FALSE;

	public function __construct($message = null, $code = 0, Exception $previous = null) {

		if ( !$this->_text)
			$this->_text = '';

		$sContext = str_replace('\\', '-', get_class($this)).' ('. basename($this->getFile()).' ~ '.$this->getLine().')';
		if ( $this->_text == '')
			$this->_text = $sContext;
		else
			$this->_text = $sContext . ' :: ' . $this->_cms;

		if ( 0 !== strlen( $message))
			$this->_text .= ' : ' . (string)$message;

		// make sure everything is assigned properly
		parent::__construct( $this->_text, $code, $previous);

	}

}
