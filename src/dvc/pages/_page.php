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
use Response;

class _page {
    protected $boolHeader = false;

    protected $boolpageHeader = false;

    protected $contentOPEN = false;

	protected $dvc = '4';

    protected $hasTitleBar = false;

    protected $headerOPEN = false;

    protected $sectionNAME = '';

	protected $sectionOPEN = false;

    protected $sectionTAG = '';

    public $bodyClass = false;

    public $closeContentTags = [];

    public $closeTags = [];

    public $css = [];

    public $charset = false;

    public $data = false;

    public $footer = true;

    public $meta = [];

    public $latescripts = [];

	public $scripts = [];

    public $title = '';

    static $docType = false;

    static $footerTemplate = '';

	static $momentJS = false;	// load momentJS sources
	static $FullCalendar = false;	// load fullCalendar sources, set to 4 for version 4

	protected function _pagefooter() {
		return $this
			->header()
			->pageHeader()
			->closeSection()
			->closeContent();

	}

	protected function close() {
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
		$this->closeSection();
		if ( $this->contentOPEN ) {
			foreach ( $this->closeContentTags as $tag )
				print $tag;

			$this->contentOPEN = false;

		}

		return ( $this);	// chain

	}

	protected function closeHeader() {
		if ( $this->headerOPEN) {
			$this->headerOPEN = false;
			printf( '%s</head>%s', PHP_EOL, PHP_EOL);

		}

		return ( $this);

	}

	protected function closeSection() {
		if ( $this->sectionOPEN ) {
			printf( '%s	</%s><!-- %s -->%s%s',
				PHP_EOL,
				$this->sectionTAG,
				$this->sectionNAME,
				PHP_EOL,
				PHP_EOL );

		}

		$this->sectionOPEN = false;

		return ( $this);	// chain

	}

	protected function library() {
		/*
		 * momentJS is required for fullCalendar
		 * otherwise optional
		 */
		if ( self::$momentJS || ( self::$FullCalendar &&  4 != (int)self::$FullCalendar)) {
			$this->scripts[] = sprintf( '<script type="text/javascript" src="%s"></script>', \strings::url('js/moment.min.js'));

		}

		$this->scripts[] = sprintf( '<script type="text/javascript" src="%s"></script>', \strings::url( 'assets/brayworth/js'));
		$this->scripts[] = sprintf( '<script type="text/javascript" src="%s"></script>', \strings::url( 'assets/brayworth/dopo'));

		if ( '5' == \config::$FONTAWESOME) {
			$this->css[] = sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', \strings::url( 'fontawesome5/css/fontawesome-all.css'));

		}
		else {
			$this->css[] = sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', \strings::url( 'css/font-awesome.min.css'));

		}

		if ( $this->dvc == '4') {
			$this->css[] = sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', \strings::url( 'assets/brayworth/css'));

		}
		elseif ( $this->dvc) {
			if ( \cssmin::dvc()) {
				$this->css[] = sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', \cssmin::$dvcmin );

			}
			else {
				$this->css[] = '<!-- no minified library :: normally we would bundle the css -->';
				foreach ( \cssmin::$dvcminFiles as $src)
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

	protected function open() {
		$this->boolOpen = true;

	}

    public function __construct( string $title = '') {
		$this->data = (object)[
            'title' => $this->title = ( $title == '' ? \config::$WEBNAME : $title )

        ];

		$this->meta[] = '<meta http-equiv="X-UA-Compatible" content="IE=edge" />';
		$this->meta[] = '<meta http-equiv="Content-Language" content="en" />';
		$this->meta[] = '<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />';

        $this->scripts[] = sprintf( '<script type="text/javascript" src="%s"></script>', \strings::url( 'assets/jquery'));

        $css = strings::url( 'assets/bootstrap/css');
        array_unshift( $this->css, sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', $css));

        $js = strings::url( 'assets/bootstrap/js');
		$this->latescripts[] = sprintf( '<script type="text/javascript" src="%s"></script>', $js);

		$this->library();

    }

    public function __destruct() {
		$this->close();

	}

	public function content( $class = null, $more = '') {
		if ( is_null( $class)) $class = 'content';

		$this
			->header()
			->closeSection()
			->openContent()
			->section( 'content', $class, 'content', $more);

		return ( $this);	// chain

	}

	public function header( bool $boolCloseHeader = true ) {
		if ( $this->boolHeader )
			return ( $this);

		$this->boolHeader = true;
		$this->headerOPEN = true;

		$this->open();
		\Response::html_headers( $this->charset);

		print self::$docType ? self::$docType : Response::html_docType();

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

	public function main( $class = 'main') {
		$this->newSection( $name = 'main', $class, $role = 'main', $more = '', $tag = 'main');

	}

	public function newSection( $name = 'content',
		$class = 'content',
		$role = 'content',
		$more = '',
		$tag = 'div'
		) {
		$this
			->header()
			->closeSection()
			->openContent()
			->section( $name, $class, $role, $more, $tag);

		return ( $this);	// chain

	}

	public function pagefooter() {
		$this->_pagefooter();

        if ( self::$footerTemplate) {
            $v = new \view;
			$v->load( self::$footerTemplate ? self::$footerTemplate : 'footer');

        }

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

	public function primary( $class = null, $more = '') {
		if ( is_null( $class)) $class = 'content-primary';

		$this
			->header()
			->closeSection()
			->openContent()
			->section( 'content-primary', $class, 'content-primary', $more);

		return ( $this);	// chain

	}

    protected function openContent() {
		$this->contentOPEN = true;

		return ( $this);

    }

	public function secondary( $class= null, $more = '') {
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

	public function section(
		string $name = 'content',
		string $class = 'content',
		string $role = 'content',
		string $more = '',
		string $tag = 'div'
		) {

		$this->closeSection();
		$this->sectionOPEN = true;
		$this->sectionNAME = $name;
		$this->sectionTAG = $tag;

		printf( '	<%s class="%s" data-role="%s" %s><!-- %s -->%s',
			$tag, $class, $role,
			$more,
			$this->sectionNAME,
			PHP_EOL

		);

		return ( $this);	// chain

    }

	public function title( $navbar = ['navbar-default']) {
		if ( !$this->boolHeader )
			$this->header();

		$this->pageHeader();

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
