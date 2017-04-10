<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/

NameSpace dvc\html;

class page extends element {
	protected $head, $body;

	function __construct() {
		parent::__construct( 'html' );
			$this->attributes( array( 'lang' => 'en' ));

		$this->head = new element( 'head' );
		$this->body = new element( 'body' );

		parent::appendChild( $this->head );
		parent::appendChild( $this->body );

	}

	public function appendChild( element $element ) {
		$this->body->appendChild( $element );

	}

	public function addChild( element $element ) {
		$this->appendChild( $element );

	}

	function __destruct() {
		print '<!DOCTYPE html>';
		if ( !$this->_rendered )
			$this->render();

	}

	function body() {
		return $this->body;

	}

}
