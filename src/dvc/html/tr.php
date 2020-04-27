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

class tr extends element {
	function __construct( $content = null, $attributes = null) {
		parent::__construct( 'tr', $content, $attributes);

	}

	public function td( $content = null, $attributes = null ) {
		$td = new element( 'td', $content, $attributes );
		$this->appendChild( $td );

		return ( $td );

	}

	public function cell( $content = null, $attributes ) {
		return ( $this->td( $content));

	}

}
