<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/

NameSpace dvc\html;

class tr extends element {
	function __construct( $content = NULL, $attributes = NULL) {
		parent::__construct( 'tr', $content, $attributes);

	}

	public function td( $content = NULL, $attributes = NULL ) {
		$td = new element( 'td', $content, $attributes );
		$this->appendChild( $td );

		return ( $td );

	}

	public function cell( $content = NULL, $attributes ) {
		return ( $this->td( $content));

	}

}
