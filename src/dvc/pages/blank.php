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

class blank extends _page {
	function __construct( $title = '' ) {
		parent::__construct( $title );

		//~ $this->scripts = [];
		$this->latescripts = [];
		$this->css = [];
		//~ $this->css[] = sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s?v=%s" />', url::tostring('css/jquery-ui.min.css'), config::$VERSION );
		$this->css[] = sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', \url::tostring( 'css/min.css' ));

		//~ $this->scripts[] = sprintf( '<script type="text/javascript" src="%s?v=%s"></script>', url::tostring('js/jquery-ui.min.js'),  config::$VERSION );

	}

	public function pagefooter() {}

}
