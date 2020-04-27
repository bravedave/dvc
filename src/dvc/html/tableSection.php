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

abstract class tableSection extends element {
	protected $_rows;

	public function __construct( $tag ) {
		parent::__construct( $tag );
		$this->_rows = array();

	}

	public function tr() {
		$tr = new tr();
		$this->_rows[] = $tr;
		$this->appendChild( $tr );

		return ( $tr );

	}

	public function row() {
		return ( $this->tr());

	}

}
