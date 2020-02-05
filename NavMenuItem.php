<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/
class NavMenuItem extends dvc\menuitem {
	function __construct( $label, $url = FALSE, $style = '' ) {
		parent::__construct( $label, $url, $style );
		$this->className = '';

	}

}
