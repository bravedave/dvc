<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	DO NOT change this file
	Copy it to <application>/app/dvc/ and modify it there
	*/
NameSpace dvc\pages;

class bootstrap extends _page {
	function __construct( $title = '' ) {
		parent::__construct( $title );

		array_unshift( $this->css, sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', url::$URL . 'css/bootstrap.min.css' ));
		$this->latescripts[] = sprintf( '<script type="text/javascript" src="%s"></script>', url::$URL . 'js/bootstrap.min.js' );

	}

	public function title( $navbar =  'navbar-inverse') {
		parent::title( $navbar);
		return ( $this);

	}

}
