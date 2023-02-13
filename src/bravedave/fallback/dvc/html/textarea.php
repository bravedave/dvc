<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/

NameSpace dvc\html;

class textarea extends element {
	function __construct( $content = NULL, $attributes = NULL ) {
		$defaultAttributes = array( 'class' => 'form-control textarea',
			'rows' => '3' );

		parent::__construct( 'textarea', $content, $defaultAttributes );

		if ( !is_null( $attributes))
			$this->attributes( $attributes);

	}

}
