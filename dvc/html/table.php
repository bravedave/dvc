<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/

NameSpace dvc\html;

class table extends element {
	protected $_head, $_body;

	public function __construct( $class = NULL ) {
		parent::__construct( 'table' );

		if ( !( is_null( $class )))
			$this->attributes( array( 'class' => (string)$class ));

		$this->_head = new thead();
		$this->_body = new tbody();

		$this->appendChild( $this->_head );
		$this->appendChild( $this->_body );

	}

	public function head() {
		return $this->_head;

	}


	public function body() {
		return $this->_body;

	}

	public function tr() {
		return ( $this->body()->tr());

	}

	public function row() {
		return ( $this->tr());

	}

}
