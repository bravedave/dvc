<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/

NameSpace dvc\html;

class img extends element {
	function __construct( $src = '', $alt = NULL ) {
		parent::__construct( 'img' );

		if ( is_null( $alt ))
			$alt = $src;

		$this->attributes( array( 'src' => $src, 'alt' => $alt ));

	}

}
