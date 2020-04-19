<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * This work is licensed under a Creative Commons Attribution 4.0 International Public License.
 *      http://creativecommons.org/licenses/by/4.0/
 *
*/

namespace dvc\html;

class nav extends element {
	function __construct( $attributes = null ) {
		parent::__construct( 'nav', null, [ 'class' => 'navbar navbar-light navbar-fixed-top', 'role' => 'navigation'] );
		if ( !is_null( $attributes))
			$this->attributes( $attributes);

	}

}
