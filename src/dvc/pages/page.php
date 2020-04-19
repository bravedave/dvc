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

class page extends _page {

	protected $boolOpen = false;

	protected $dvc = '3';

	protected $topOfPage = [];

	protected static $developer = false;

	public $timer = null;

	public $debug = false;

	public $jQuery2 = false;

	public static $Bootstrap_Version = '3';

	public static $MainContextMenu = true;

	public static $BootStrap = false;

	public static $pageContainer = '';

	function __construct( $title = '' ) {

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

		$this->meta[] = '<meta name="page-constructor" content="_default" />';
		$this->topOfPage[] = '	<div id="top-of-page"></div>';

		$aCss = [ 'custom'];
		if ( \application::app()) {
			$aCss[] = \application::app()->controller();

		}

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

			$this->headerOPEN = false;

			printf( '%s</head>%s', PHP_EOL, PHP_EOL);

			/* this is a bit legacy-ish
				originally closeheader opened the page */
			$this->pageHeader();

		}

		return ( $this);

	}

	public function isOpen() {
		return ( (bool)$this->boolOpen);

	}

	public function main( $class = 'main') {
		$this->newSection( $name = 'main', $class, $role = 'main');

	}

	public function pageHeader() {
		if ( $this->boolpageHeader )
			return ( $this);

		$ret = parent::pageHeader();
		foreach ( $this->topOfPage as $s) {
			print $s . PHP_EOL;

		}

		return ( $ret);

	}

	public function title( $navbar = '') {
		return ( parent::title( $navbar ? $navbar : 'navbar'));

	}

	protected function openContent() {
		if ( !$this->contentOPEN ) {
			$this->contextmenu();

			if ( self::$BootStrap) {

				$this->closeContentTags[] = '	</div></div><!-- /_page:Main Content Area -->' . PHP_EOL;

				$classes = [];

				if ( (int)self::$Bootstrap_Version == 3) $classes[] = 'main-content-wrapper';

				if ( self::$pageContainer) {
					$classes[] = self::$pageContainer;

				}

				if ( $this->hasTitleBar && (int)self::$Bootstrap_Version == 3) $classes[] = 'with-nav-bar';

				printf( '%s%s	<div class="%s" data-role="main-content-wrapper"><div class="row"><!-- _page:Main Content Area -->%s', PHP_EOL, PHP_EOL, implode( ' ', $classes), PHP_EOL);

			}
			else {

				$this->closeContentTags[] = '	</div><!-- /_page:Main Content Area -->' . PHP_EOL;

				$classes = ['main-content-wrapper'];
				if ( $this->hasTitleBar) {
					$classes[] = 'with-nav-bar';

				}

				printf( '%s%s	<div class="%s" data-role="main-content-wrapper"><!-- _page:Main Content Area -->%s', PHP_EOL, PHP_EOL, implode( ' ', $classes), PHP_EOL);

			}

			$this->contentOPEN = TRUE;

		}

		return ( $this);

	}

	public function pagefooter() {
		if ( '' == self::$footerTemplate) self::$footerTemplate = 'footer';
		return ( parent::pagefooter());	// chain

	}

	public function menu() {}
	public function contextmenu() {}

}
