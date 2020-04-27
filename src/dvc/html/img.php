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

class img extends element {
	function __construct( $src = '', $alt = null ) {
		parent::__construct( 'img' );

		if ( is_null( $alt ))
			$alt = $src;

		$this->attributes( ['src' => $src, 'alt' => $alt ]);

	}

}
