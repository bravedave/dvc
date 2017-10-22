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
	public function __construct( $sMessage = '', $iCode = 0, $oPrevious = null) {
		$sMessage = 0 === strlen( $sMessage) ? str_replace('\\', '-', get_class($this)).' ('.
			basename($this->getFile()).' ~ '.$this->getLine().')' : $sMessage;

		parent::__construct( $sMessage, $iCode, $oPrevious);

	}

}
