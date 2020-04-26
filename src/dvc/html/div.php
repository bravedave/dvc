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

class div extends element {
	function __construct( $content = null, $attributes = null) {
		parent::__construct( 'div', $content, $attributes );

	}

}
