<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/

NameSpace dvc\html;

class success extends element {
	function __construct( $content = NULL ) {
		parent::__construct( 'div' );

		$this->append( 'div', 'Success', array( 'class' => 'panel-heading' ));
		$this->append( 'div', $content, array( 'class' => 'panel-body' ));

		$this->attributes( array( 'class' => 'panel panel-success' ));

	}

}
