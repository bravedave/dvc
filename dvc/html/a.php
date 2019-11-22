<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/

namespace dvc\html;

class a extends element {
	function __construct( $href = '', $content = null, $attributes = null ) {
		if ( is_null( $content ))
			$content = $href;

		parent::__construct( 'a', $content, ['href' => $href]);

		if ( !is_null( $attributes ))
			$this->attributes( $attributes);

	}

}
