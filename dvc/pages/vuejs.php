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

class vuejs extends page {
	static public $BootStrap_Version = '4';

	function __construct( $title = '' ) {
		self::$vuejs = TRUE;
		$this->dvc = '4';

		$this->_footer = NULL;

		parent::__construct( $title);
		$css = [ \url::tostring( sprintf( 'bootstrap.%s/css/bootstrap.min.css', self::$BootStrap_Version))];

		foreach ( $css as $c)
			array_unshift( $this->css, sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', $c));

	}

	public function title( $navbar = 'navbar-vue') {
		parent::title( $navbar);

	}

	public function pagefooter() {
		$this
			->header()
			->pageHeader()
			->closeSection()
			->closeContent();

	}

}
