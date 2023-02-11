<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc\pages;

class bootstrap4_app extends bootstrap4 {

	function __construct( $title = '' ) {
    self::$pageContentTag = 'main';
		parent::__construct( $title);

		$this->meta[] = '<meta name="apple-mobile-web-app-capable" content="yes" />';

  }

	public function pagefooter() {
		$this->_pagefooter();

		if ( '' == self::$footerTemplate) {
			self::$footerTemplate = 'footer-shell';

		}

		return ( parent::pagefooter());	// chain

	}

	public function title( $navbar = 'navbar-shell') {
		if ( !$navbar) {
			$navbar = 'navbar-shell';

    }

		return ( parent::title( $navbar));

	}

}
