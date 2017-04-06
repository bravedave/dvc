<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/

NameSpace dvc\html;

class lists extends element {
	function additem( $text = NULL, $attributes = NULL ) {
		return ( $this->add( $text, $attributes ));

	}

	function add( $text = NULL, $attributes = NULL ) {
		return ( $this->append( 'li', $text, $attributes ));

	}

}
