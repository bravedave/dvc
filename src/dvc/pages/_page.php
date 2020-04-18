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

use strings;

class _page extends _core {
	protected $dvc = '3';

	public $debug = false,
		$jQuery2 = false;

	function __construct( string $title = '' ) {
		parent::__construct( $title);

		if ( \userAgent::isLegacyIE()) {
			$this->scripts = [
				sprintf( '<script type="text/javascript" src="%s"></script>', strings::url( 'js/jquery-1.11.3.min.js'))

			];

		}
		elseif ( $this->jQuery2) {
			$this->scripts = [
				sprintf( '<script type="text/javascript" src="%s"></script>', strings::url( 'js/jquery-2.2.4.min.js'))

			];

		}

		$this->latescripts = [];	// legacy starts with blank page

		$this->library();

	}

	public function content( $class = null, $more = '') {
		if ( is_null( $class)) $class = 'content';

		$this
			->header()
			->pageHeader()
			->closeSection()
			->openContent()
			->section( 'content', $class, 'content', $more);

		return ( $this);	// chain

	}

	public function isOpen() {
		return ( (bool)$this->boolOpen);

	}

	public function main( $class = 'main') {
		$this->newSection( $name = 'main', $class, $role = 'main');

	}

	protected function openContent() {
		if ( $this->contentOPEN )
			return ( $this);

		$this->closeContentTags[] = '	</div><!-- /_page:Main Content Area -->' . PHP_EOL;
		$classes = ['main-content-wrapper'];
		if ( $this->hasTitleBar)
			$classes[] = 'with-nav-bar';

		printf( '%s%s	<div class="%s" data-role="main-content-wrapper"><!-- _page:Main Content Area -->%s', PHP_EOL, PHP_EOL, implode( ' ', $classes), PHP_EOL);

		return parent::openContent();

	}

	public function pagefooter() {
		if ( !self::$footerTemplate) self::$footerTemplate = 'footer';
		return ( parent::pagefooter());	// chain

	}

}
