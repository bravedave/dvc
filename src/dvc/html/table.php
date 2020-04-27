<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc\html;

class table extends element {
	protected $_head, $_body;

	public function __construct( $class = null ) {
		parent::__construct( 'table' );

		if ( !( is_null( $class )))
			$this->attributes( ['class' => (string)$class ]);

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
