<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/

NameSpace dvc\html;

class nav extends element {
	function __construct( $attributes = NULL ) {
		parent::__construct( 'nav', NULL, array( 'class' => 'navbar navbar-default navbar-fixed-top', 'role' => 'navigation' ));
		if ( !is_null( $attributes))
			$this->attributes( $attributes);

	}

}
