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

class NavMenuItem extends dvc\menuitem {
	function __construct( $label, $url = FALSE, $style = '' ) {
		parent::__construct( $label, $url, $style );
		$this->className = '';

	}

}
