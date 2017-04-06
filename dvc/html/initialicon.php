<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/

NameSpace dvc\html;

class initialicon extends element {
	static $debug = TRUE;

	static function rendered( $content = NULL, $attributes = NULL) {
		$i = new initialicon( $content, $attributes );
		return ( $i->render( TRUE));

	}

	function __construct( $content = NULL, $attributes = NULL ) {
		parent::__construct( 'div', $content, array( 'class' => 'initial-icon',
			'style' => sprintf('background-color: hsl(%d, %s, %s)',
				(int)( abs( crc32 ( $content)) * 22 )% 356,
				'100%',
				'30%'
				) ));

		if ( !is_null( $attributes ))
			$this->attributes( $attributes);

	}

}
