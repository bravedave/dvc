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
	function __construct( $title = '' ) {
		self::$vuejs = TRUE;
		$this->_footer = NULL;

		parent::__construct( $title);

	}

	public function title( $navbar = 'navbar-vue') {
		parent::title( $navbar);

	}

	public function pagefooter() {
		$this
			->header()
			->pageHeader();

	}

}