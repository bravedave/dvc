<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/

NameSpace dvc\html;

class input extends element {
	function __construct( $type = 'text', $attributes = NULL ) {
		parent::__construct( 'input', NULL, $attributes );

		$this->attributes( array( 'type' => $type ));

		$this->selfClosing = TRUE;

	}

}
