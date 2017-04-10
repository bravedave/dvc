<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/

NameSpace dvc\html;

class jumbotron extends element {
	function __construct( $content = NULL ) {
		parent::__construct( 'div', $content, array( 'class' => 'jumbotron' ));

	}

}
