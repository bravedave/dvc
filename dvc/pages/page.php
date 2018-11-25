<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/
namespace dvc\pages;

class page extends _page {
	public $timer = null;

	protected $boolOpen = false;

	static public $MainContextMenu = true;
	static public $BootStrap = false;
	static public $pageContainer = '';

	protected static $developer = false;
	protected $topOfPage = [];

	function __construct( $title = '' ) {
		parent::__construct( $title);

		$this->meta[] = '<meta name="page-constructor" content="_default" />';
		$this->topOfPage[] = '	<div id="top-of-page"></div>';

		$aCss = [ 'custom'];
		if ( \application::app())
		$aCss[] = \application::app()->controller();

		foreach ( $aCss as $cssFile) {
			if ( file_exists( $_file = realpath( '.' ) . '/css/' . $cssFile . '.css' )) {
				$modtime = filemtime( $_file);
				$this->css[] = sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', \url::tostring( sprintf( 'css/%s.css?v=%s', $cssFile, $modtime)));

			}
			else {
				if ( file_exists( $_file = \application::app()->getRootPath() . '/app/public/css/' . $cssFile . '.css' )) {
					$modtime = filemtime( $_file);
					$this->css[] = sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', \url::tostring( sprintf( 'css/%s.css?v=%s', $cssFile, $modtime )));

				}

			}

		}

	}

	public function closeHeader() {
		if ( $this->headerOPEN) {

			$this->headerOPEN = FALSE;

			printf( '%s</head>%s', PHP_EOL, PHP_EOL);

			/* this is a bit legacy ish
				originally closeheader opened the page */
			$this->pageHeader();

		}

		return ( $this);

	}

	public function pageHeader() {
		if ( $this->boolpageHeader )
			return ( $this);

		$ret = parent::pageHeader();
		foreach ( $this->topOfPage as $s)
			print $s . PHP_EOL;

		return ( $ret);

	}

	public function title( $navbar = '') {
		if ( (string)$navbar == '') {
			$navbar = 'navbar-default';

		}

		return ( parent::title( $navbar));

	}

	protected function openContent() {
		if ( !$this->contentOPEN ) {
			$this->contextmenu();

			if ( self::$BootStrap) {

				$this->closeContentTags[] = '	</div></div><!-- /_page:Main Content Area -->' . PHP_EOL;

				$classes = [];

				if ( (int)\config::$BOOTSTRAP_VERSION == 3) $classes[] = 'main-content-wrapper';

				if ( self::$pageContainer) {
					$classes[] = self::$pageContainer;

				}

				if ( $this->hasTitleBar && (int)\config::$BOOTSTRAP_VERSION == 3) $classes[] = 'with-nav-bar';

				printf( '%s%s	<div class="%s" data-role="main-content-wrapper"><div class="row"><!-- _page:Main Content Area -->%s', PHP_EOL, PHP_EOL, implode( ' ', $classes), PHP_EOL);

			}
			else {

				$this->closeContentTags[] = '	</div><!-- /_page:Main Content Area -->' . PHP_EOL;

				$classes = ['main-content-wrapper'];
				if ( $this->hasTitleBar)
				$classes[] = 'with-nav-bar';

				printf( '%s%s	<div class="%s" data-role="main-content-wrapper"><!-- _page:Main Content Area -->%s', PHP_EOL, PHP_EOL, implode( ' ', $classes), PHP_EOL);

			}

			$this->contentOPEN = TRUE;

		}

		return ( $this);

	}

	public function content( $class = null, $more = null) {
		if ( is_null( $class)) {
			$class = 'content';

		}

		$this
			->header()
			->closeSection()
			->openContent()
			->section( 'content', $class, 'content', $more);

		return ( $this);	// chain

	}

	public function primary( $class = null, $more = null) {
		if ( is_null( $class)) {
			$class = 'content-primary';

		}

		$this
			->header()
			->closeSection()
			->openContent()
			->section( 'content-primary', $class, 'content-primary', $more);

		return ( $this);	// chain

	}

	public function secondary( $class= null, $more = null) {
		if ( is_null( $class)) {
			$class = 'content-secondary';

		}

		$this
			->header()
			->closeSection()
			->openContent()
			->section( 'content-secondary', $class, 'content-secondary', $more);

		return ( $this);	// chain

	}

	public function pagefooter() {
		$this->_pagefooter();

		if ( '' == self::$footerTemplate) {
			self::$footerTemplate = 'footer';

		}

		return ( parent::pagefooter());	// chain

	}

	public function menu() {}
	public function contextmenu() {}

}
