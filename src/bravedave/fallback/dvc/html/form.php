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
use strings;

class form extends element {
	function __construct( $action = null, $attributes = null ) {

		if ( is_null( $attributes))
			$attributes = [];

		if ( !is_null( $action))
			$attributes['action'] = $action;

		if ( !isset( $attribute['id']))
			$attributes['id'] = strings::rand();

		parent::__construct( 'form', null, $attributes );

	}

}
