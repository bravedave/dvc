<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/
Namespace dao\dto;

class mailmessage extends _dto {
	var $Flags = array();
	var $Folder = '';
	var $From = '';
	var $To = '';
	var $Uid = '';
	var $MessageID = '';
	var $Date = '';
	var $Html = '';
	var $Plain = '';
	var $Attachments = '';
	var $tags = '';

	public function brief( $length = 100 ) {
		$debug = FALSE;
		//~ $debug = TRUE;

		if ( $debug) \sys::logger( sprintf( 'dao\dto\brief ( %s)', $length));

		$plainText = $this->Plain;
		if ( strlen( $plainText) < 1)
			$plainText = trim( strip_tags( \utility::htmlSanitize( $this->Html)));

		return \strings::brief( $plainText);

	}

	public function seen() {
		return ( in_array( '\Seen', $this->Flags));

	}

	public function answered() {
		return ( in_array( '\Answered', $this->Flags));

	}

	public function flagged() {
		return ( in_array( '\Flagged', $this->Flags));

	}

	public function forwarded() {
		return ( in_array( '$Forwarded', $this->Flags));

	}

}
