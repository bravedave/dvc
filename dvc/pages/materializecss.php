<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/
NameSpace dvc\pages;

class materializecss extends _page {
	function __construct( $title = '' ) {
		parent::__construct( $title );

		$a = array();
		foreach ( $this->css as $css) {
			if ( !( preg_match( '/(dvc.css|custom.css)/', $css )))
				$a[] = $css;

		}

		$this->css = $a;

		$this->meta[] = '<meta name="viewport" content="width=device-width, initial-scale=1" />';

		// Import materialize.css
		array_unshift( $this->css, sprintf( '<link type="text/css" rel="stylesheet" href="%scss/materialize.min.css"  media="screen,projection" />', \url::$URL ));
		// Import Google Icon Font
		array_unshift( $this->css, sprintf( '<link href="//fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />', \url::$URL ));

		// append materialize.js
		$this->latescripts[] = sprintf( '<script type="text/javascript" src="%sjs/materialize.min.js"></script>', \url::$URL );

	}

	public function pagefooter() {}

}
