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
use dvc\cssmin;

class _page {
	protected $boolHeader = false,
		$boolpageHeader = false,
		$headerOPEN = false,
		$contentOPEN = false,
		$sectionOPEN = false,
		$sectionNAME = '',
		$hasTitleBar = false,
		$dvc = '3';

	public $title = '';
	public $data = false;
	public $charset = false;

	public $meta = [],
		$scripts = [],
		$latescripts = [],
		$css = [],
		$closeTags = [],
		$closeContentTags = [],
		$footer = true,
		$bodyClass = false,
		$debug = false,
		$jQuery2 = false;

	static $docType = false;

	static $momentJS = false;	// load momentJS sources
	static $FullCalendar = false;	// load fullCalendar sources, set to 4 for version 4
	static $footerTemplate = '';

	function __construct( $title = '' ) {
		$this->data = (object)['title' => ''];

		$this->data->title = $this->title = ( $title == '' ? \config::$WEBNAME : $title );

		$this->meta[] = '<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />';
		$this->meta[] = '<meta http-equiv="Content-Language" content="en" />';

		if ( \userAgent::isLegacyIE()) {
			$this->scripts[] = sprintf( '<script type="text/javascript" src="%s"></script>', \strings::url( 'js/jquery-1.11.3.min.js'));

		}
		elseif ( $this->jQuery2) {
			$this->scripts[] = sprintf( '<script type="text/javascript" src="%s"></script>', \strings::url( 'js/jquery-2.2.4.min.js'));

		}
		else {
			$this->scripts[] = sprintf( '<script type="text/javascript" src="%s"></script>', \strings::url( 'assets/jquery'));

		}

		/*
		 * momentJS is required for fullCalendar
		 * otherwise optional
		 */
		if ( self::$momentJS || ( self::$FullCalendar &&  4 != (int)self::$FullCalendar)) {
			$this->scripts[] = sprintf( '<script type="text/javascript" src="%s"></script>', \strings::url('js/moment.min.js'));

		}

		$this->scripts[] = sprintf( '<script type="text/javascript" src="%s"></script>', \strings::url( 'assets/brayworth/js'));
		$this->scripts[] = sprintf( '<script type="text/javascript" src="%s"></script>', \strings::url( 'assets/brayworth/dopo'));

    $this->css[] = sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', \strings::url( 'css/font-awesome.min.css'));

		if ( $this->dvc == '4') {
			$this->css[] = sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', \strings::url( 'assets/brayworth/css'));

		}
		elseif ( $this->dvc) {
			if ( cssmin::dvc()) {
				$this->css[] = sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', cssmin::$dvcmin );

			}
			else {
				$this->css[] = '<!-- no minified library :: normally we would bundle the css -->';
				foreach ( cssmin::$dvcminFiles as $src)
					$this->css[] = sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', \strings::url( $src));

			}

		}
		if ( 4 == (int)self::$FullCalendar) {
			$this->css[] = sprintf('<link type="text/css" rel="stylesheet" href="%s" />', \strings::url('assets/fullcalendar/css'));
			$this->scripts[] = sprintf( '<script type="text/javascript" src="%s"></script>', \strings::url('assets/fullcalendar/js'));

		}
		elseif ( self::$FullCalendar ) {
			$this->css[] = sprintf( '<link type="text/css" rel="stylesheet" href="%s" />', \strings::url('fullcalendar/fullcalendar.min.css'));
			$this->css[] = sprintf('<link type="text/css" rel="stylesheet" href="%s" media="print" />', \strings::url('fullcalendar/fullcalendar.print.css'));
			$this->scripts[] = sprintf( '<script type="text/javascript" src="%s"></script>', \strings::url('fullcalendar/fullcalendar.min.js'));

		}

	}

	function __destruct() {
		$this->close();

	}

	protected function _pagefooter() {
		return $this
			->header()
			->pageHeader()
			->closeSection()
			->closeContent();

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

	protected function closeContent() {
		$this->closeSection();	// added 20 July, 2017
		if ( $this->contentOPEN ) {
			foreach ( $this->closeContentTags as $tag )
				print $tag;

			$this->contentOPEN = FALSE;

		}

    if ( $this->debug) \sys::logger( __METHOD__);

		return ( $this);	// chain

	}

	public function closeHeader() {
		if ( $this->headerOPEN) {
			$this->headerOPEN = false;
			printf( '%s</head>%s', PHP_EOL, PHP_EOL);

		}

		return ( $this);

	}

	public function closeSection() {
		if ( $this->sectionOPEN )
			printf( '%s	</div><!-- %s -->%s%s', PHP_EOL, $this->sectionNAME, PHP_EOL, PHP_EOL );

		$this->sectionOPEN = false;

		return ( $this);	// chain

	}

	public function content( $class = null, $more = null) {
		if ( is_null( $class)) $class = 'content';

		$this
			->header()
			->pageHeader()
			->closeSection()
			->openContent()
			->section( 'content', $class, 'content', $more);

		return ( $this);	// chain

	}

	public function header( $boolCloseHeader = true ) {
		if ( $this->boolHeader )
			return ( $this);

		$this->boolHeader = true;
		$this->headerOPEN = true;

		$this->open();
		\Response::html_headers( $this->charset);

		print self::$docType ? self::$docType : \Response::html_docType();

		printf( '%s<head>%s', PHP_EOL, PHP_EOL);

		if ( !$this->charset)
      $this->charset = 'utf-8';

    $this->meta[] = sprintf( '<meta charset=%s" />', $this->charset);
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

	public function isOpen() {
		return ( (bool)$this->boolOpen);

	}

	public function open() {
		$this->boolOpen = true;

	}

	protected function openContent() {
		if ( $this->contentOPEN )
			return ( $this);

		$this->closeContentTags[] = '	</div><!-- /_page:Main Content Area -->' . PHP_EOL;
		$classes = ['main-content-wrapper'];
		if ( $this->hasTitleBar)
			$classes[] = 'with-nav-bar';

		printf( '%s%s	<div class="%s" data-role="main-content-wrapper"><!-- _page:Main Content Area -->%s', PHP_EOL, PHP_EOL, implode( ' ', $classes), PHP_EOL);

		$this->contentOPEN = true;

		return ( $this);

	}

	public function newSection( $name = 'content', $class = 'content', $role = 'content', $more = '') {
		$this
			->header()
			->closeSection()
			->openContent()
			->section( $name, $class, $role, $more);

		return ( $this);	// chain

	}

	public function section( $name = 'content', $class = 'content', $role = 'content', $more = '') {
		$this->closeSection();
		$this->sectionOPEN = true;
		$this->sectionNAME = $name;

		printf(
      '	<div class="%s" data-role="%s" %s>%s',
      $class,
      $role,
      $more,
      PHP_EOL

    );

		return ( $this);	// chain

	}

	public function pagefooter() {
		$this->_pagefooter();

		$v = new \view;
			$v->load( self::$footerTemplate ? self::$footerTemplate : 'footer');

		return ( $this);	// chain

	}

	public function pageHeader() {
		if ( $this->boolpageHeader )
			return ( $this);

		$this->boolpageHeader = true;

		$this->closeHeader();

		if ( $this->bodyClass)
			printf( '<body class="%s">%s', $this->bodyClass, PHP_EOL);

		else
			print '<body>' . PHP_EOL;

		return ( $this);

	}

	public function title( $navbar = 'navbar-default') {
    $this
      ->header()
      ->pageHeader();

    if ( $this->debug) \sys::logger( sprintf('<%s> %s', $navbar, __METHOD__));

    if ( $navbar) {
      $v = new \view( $this->data);
        $v->title = $this->title;
        foreach( (array)$navbar as $_){
          $v->load( $_);

        }

      $this->hasTitleBar = true;

    }

		return ( $this);

	}

}
