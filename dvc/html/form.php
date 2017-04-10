<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/

NameSpace dvc\html;

class form extends element {
	function __construct( $action = NULL, $attributes = NULL ) {

		if ( is_null( $attributes))
			$attributes = array();

		if ( !is_null( $action))
			$attributes['action'] = $action;

		parent::__construct( 'form', NULL, $attributes );

	}

}
