<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/
NameSpace dvc\pages;

class materializecss extends page {
	function __construct( $title = '' ) {
		parent::__construct( $title);

		$a = array();
		foreach ( $this->css as $css) {
			if ( !( preg_match( '/(dvc.css|dvc.min.css|custom.css)/', $css )))
				$a[] = $css;

		}

		$this->css = $a;

		$this->meta[] = '<meta name="viewport" content="width=device-width, initial-scale=1" />';

		array_unshift( $this->css, sprintf( '<link type="text/css" rel="stylesheet" href="%s"  media="screen,projection" />', \url::tostring( 'css/materialize.min.css')));			// Import materialize.css
		array_unshift( $this->css, '<link rel="stylesheet" href="//fonts.googleapis.com/icon?family=Material+Icons" />');		// Import Google Icon Font
		$this->latescripts[] = sprintf( '<script type="text/javascript" src="%s"></script>', \url::tostring( 'js/materialize.min.js') );	// append materialize.js

	}

	public function title( $navbar =  'materialize-navbar-demo') {
		parent::title( $navbar);
		return ( $this);

	}

	public function pagefooter() {}

}
