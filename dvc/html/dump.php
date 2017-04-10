<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/

NameSpace dvc\html;

class dump extends div {
	function __construct( $content = NULL, $title = NULL ) {
		parent::__construct();

		if ( (string)$title != '' )
			$this->append( 'h1', $title );

		$this->append( 'pre', print_r( $content, TRUE ));

	}

}
