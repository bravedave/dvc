<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/
NameSpace dvc\pages;

class _page {
	protected $boolHeader = FALSE,
		$boolpageHeader = FALSE,
		$headerOPEN = FALSE,
		$contentOPEN = FALSE,
		$sectionOPEN = FALSE,
		$sectionNAME = '',
		$hasTitleBar = FALSE,
		$dvc = '3';

	public $title = '';
	public $data = FALSE;
	public $charset = FALSE;

	public $meta = [],
		$scripts = [],
		$latescripts = [],
		$css = [],
		$closeTags = [],
		$closeContentTags = [],
		$footer = TRUE,
		$bodyClass = FALSE,
		$debug = FALSE,
		$jQuery2 = FALSE;

	static $momentJS = FALSE;	// load momentJS sources
	static $FullCalendar = FALSE;	// load fullCalendar sources
	static $footerTemplate = '';

	function __construct( $title = '' ) {
		$this->data = (object)['title' => ''];

		$this->data->title = $this->title = ( $title == '' ? \config::$WEBNAME : $title );

		$this->meta[] = '<meta http-equiv="X-UA-Compatible" content="IE=edge" />';
		$this->meta[] = '<meta http-equiv="Content-Language" content="en" />';

		if ( \userAgent::isLegacyIE()) {
			$this->scripts[] = sprintf( '<script type="text/javascript" src="%s"></script>', \url::tostring( 'js/jquery-1.11.3.min.js'));

		}
		elseif ( $this->jQuery2) {
			$this->scripts[] = sprintf( '<script type="text/javascript" src="%s"></script>', \url::tostring( 'js/jquery-2.2.4.min.js'));

		}
		else {
			// $this->scripts[] = sprintf( '<script type="text/javascript" src="%s"></script>', \url::tostring( 'js/jquery-3.3.1.min.js'));
			$this->scripts[] = sprintf( '<script type="text/javascript" src="%s"></script>', \url::tostring( 'assets/jquery'));

		}

		/*
		 * momentJS is required for fullCalendar
		 * otherwise optional
		 */
		if ( self::$momentJS || self::$FullCalendar )
			$this->scripts[] = sprintf( '<script type="text/javascript" src="%s"></script>', \url::tostring('js/moment.min.js'));

		if ( \jslib::brayworth()) {
			$this->scripts[] = sprintf( '<script type="text/javascript" src="%s"></script>', \jslib::$brayworthlib );

		}
		else {
			$this->scripts[] = '<!-- no minified library :: normally we would bundle the scripts -->';
			foreach ( \jslib::$brayworthlibFiles as $src)
				$this->scripts[] = sprintf( '<script type="text/javascript" src="%s"></script>', \url::tostring( $src));

		}


		$this->css[] = sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', \url::tostring( 'css/font-awesome.min.css'));
		// $this->css[] = sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', \url::tostring( 'fontawesome5/css/fontawesome-all.css'));
		if ( $this->dvc == '4') {
			if ( \cssmin::dvc( NULL, NULL, '4')) {
				$this->css[] = sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', \cssmin::$dvcmin );

			}
			else {
				$this->css[] = '<!-- no minified library :: normally we would bundle the css -->';
				foreach ( \cssmin::$dvcminFiles as $src)
				$this->css[] = sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', \url::tostring( $src));

			}

		}
		elseif ( $this->dvc) {
			if ( \cssmin::dvc()) {
				$this->css[] = sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', \cssmin::$dvcmin );

			}
			else {
				$this->css[] = '<!-- no minified library :: normally we would bundle the css -->';
				foreach ( \cssmin::$dvcminFiles as $src)
					$this->css[] = sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', \url::tostring( $src));

			}

		}

		if ( self::$FullCalendar ) {
			$this->css[] = sprintf( '<link type="text/css" rel="stylesheet" href="%s" />', \url::tostring('fullcalendar/fullcalendar.min.css'));
			$this->css[] = sprintf('<link type="text/css" rel="stylesheet" href="%s" media="print" />', \url::tostring('fullcalendar/fullcalendar.print.css'));
			$this->scripts[] = sprintf( '<script type="text/javascript" src="%s"></script>', \url::tostring('fullcalendar/fullcalendar.min.js'));

		}

	}

	function __destruct() {
		$this->close();

	}

	public function open() {
		$this->boolOpen = TRUE;

	}

	public function isOpen() {
		return ( (bool)$this->boolOpen);

	}

	public function header( $boolCloseHeader = TRUE ) {
		if ( $this->boolHeader )
			return ( $this);

		$this->boolHeader = TRUE;
		$this->headerOPEN = TRUE;

		$this->open();
		\Response::html_headers( $this->charset);

		print \Response::html_docType();

		printf( '%s<head>%s', PHP_EOL, PHP_EOL);

		if ( !$this->charset)
			$this->charset = 'utf-8';
		$this->meta[] = sprintf( '<meta http-equiv="Content-Type" content="text/html; charset=%s" />', $this->charset);

		foreach ( $this->meta as $meta )
			print "\t" . $meta . PHP_EOL;

		printf( '	<title>%s</title>%s', $this->title, PHP_EOL);

		foreach ( $this->css as $css )
			print "\t" . $css . PHP_EOL;

		foreach ( $this->scripts as $script )
			print "\t" . $script . PHP_EOL;

		if ( $boolCloseHeader)
			$this->closeHeader();

		return ( $this );

	}

	public function closeHeader() {
		if ( $this->headerOPEN) {
			$this->headerOPEN = FALSE;
			printf( '%s</head>%s', PHP_EOL, PHP_EOL);

		}

		return ( $this);

	}

	public function pageHeader() {
		if ( $this->boolpageHeader )
			return ( $this);

		$this->boolpageHeader = TRUE;

		$this->closeHeader();

		if ( $this->bodyClass)
			printf( '<body class="%s">%s', $this->bodyClass, PHP_EOL);

		else
			print '<body>' . PHP_EOL;

		return ( $this);

	}

	public function title( $navbar = 'navbar-default') {
		if ( !$this->boolHeader )
			$this->header();

		if ( $this->debug) \sys::logger( $navbar);

		$v = new \view( $this->data);
			$v->load( $navbar);

		$this->hasTitleBar = TRUE;

		return ( $this);

	}

	protected function openContent() {
		if ( $this->contentOPEN )
			return ( $this);

		$this->closeContentTags[] = '	</div><!-- /_page:Main Content Area -->' . PHP_EOL;
		$classes = ['main-content-wrapper'];
		if ( $this->hasTitleBar)
			$classes[] = 'with-nav-bar';

		printf( '%s%s	<div class="%s" data-role="main-content-wrapper"><!-- _page:Main Content Area -->%s', PHP_EOL, PHP_EOL, implode( ' ', $classes), PHP_EOL);

		$this->contentOPEN = TRUE;

		return ( $this);

	}

	protected function closeContent() {
		$this->closeSection();	// added 20 July, 2017
		if ( $this->contentOPEN ) {
			foreach ( $this->closeContentTags as $tag )
				print $tag;

			$this->contentOPEN = FALSE;

		}

		return ( $this);	// chain

	}

	public function section( $name = 'content', $class = 'content', $role = 'content') {
		$this->closeSection();
		$this->sectionOPEN = TRUE;
		$this->sectionNAME = $name;

		printf( '		<div class="%s" data-role="%s">%s', $class, $role, PHP_EOL );

		return ( $this);	// chain

	}

	public function closeSection() {
		if ( $this->sectionOPEN )
			printf( '%s		</div><!-- %s -->%s%s', PHP_EOL, $this->sectionNAME, PHP_EOL, PHP_EOL );

		$this->sectionOPEN = FALSE;

		return ( $this);	// chain

	}

	public function content() {
		$this
			->header()
			->pageHeader()
			->closeSection()
			->openContent()
			->section( 'content', 'content', 'content');

		return ( $this);	// chain

	}

	protected function _pagefooter() {
		return $this
			->header()
			->pageHeader()
			->closeSection()
			->closeContent();

	}

	public function pagefooter() {
		$this->_pagefooter();

		$v = new \view;
			$v->load( self::$footerTemplate ? self::$footerTemplate : 'footer');

		return ( $this);	// chain

	}

	public function close() {
		$this
			->header()
			->pageHeader()
			->closeSection()
			->closeContent();

		$time = '';
		if ( $this->boolOpen ) {
			if ( $this->footer )
				$this->pagefooter();

			foreach ( $this->closeTags as $tag )
				print $tag;

			$this->closeTags = [];

			foreach ( $this->latescripts as $script )
				print "\t" . $script . PHP_EOL;

			printf( '%s</body>%s</html>%s', PHP_EOL, PHP_EOL, PHP_EOL );

		}

		$this->boolOpen = FALSE;

	}

}
