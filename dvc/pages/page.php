<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/
NameSpace dvc\pages;

class page extends _page {
	public $timer = NULL;

	public $jQuery3 = FALSE;

	protected $boolOpen = FALSE;

	static public $MainContextMenu = TRUE;

	protected static $vuejs = FALSE;
	protected $topOfPage = array();

	protected function _viewjs( $title = '' ) {
		$this->meta = array();
		$this->scripts = array();
		$this->latescripts = array();
		$this->css = array();

		//~ $this->css = array();
		$this->meta[] = '<meta name="page-constructor" content="_vuejs" />';
		$this->scripts[] = sprintf( '<script type="text/javascript" src="%s"></script>', \url::tostring( 'js/vue.js'));

	}

	protected function __default_construct( $title = '' ) {
		$this->meta[] = '<meta name="page-constructor" content="_default" />';
		$this->topOfPage[] = '	<div id="top-of-page"></div>';

		if ( \config::$CSS_BASE == 'mini') {
			$this->css = array();
			$this->css[] = sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', \url::tostring('css/mini-default.min.css'));
			$this->css[] = sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', \url::tostring('css/mini-custom.css'));

		}

		$aCss = array( 'custom');
		if ( \application::app())
			$aCss[] = \application::app()->controller();

		foreach ( $aCss as $cssFile) {
			if ( file_exists( realpath( '.' ) . '/css/' . $cssFile . '.css' ))
				$this->css[] = sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', \url::tostring( sprintf( 'css/%s.css', $cssFile)));
			elseif ( file_exists( \application::app()->getRootPath() . '/app/public/css/' . $cssFile . '.css' ))
				$this->css[] = sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', \url::tostring( sprintf( 'css/%s.css', $cssFile )));

		}

		if ( \userAgent::isLegacyIE()) {
			$this->scripts = array();
			$this->scripts[] = sprintf( '<script type="text/javascript" src="%s"></script>', \url::tostring( 'js/jquery-1.11.3.min.js'));

		}
		elseif ( $this->jQuery3) {
			$this->scripts = array();
			$this->scripts[] = sprintf( '<script type="text/javascript" src="%s"></script>', \url::tostring( 'js/jquery-3.1.1.min.js'));

		}

	}

	function __construct( $title = '' ) {
		parent::__construct( $title);

		if ( self::$vuejs)
			$this->_viewjs( $title);

		else
			$this->__default_construct( $title);

	}


	public function closeHeader() {
		if ( $this->headerOPEN) {

			$this->headerOPEN = FALSE;

			print <<<OUTPUT

</head>

OUTPUT;

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

	public function title( $navbar =  '') {
		if ( (string)$navbar == '') {
			if ( \config::$CSS_BASE == 'mini')
				$navbar = 'navbar-mini';
			else
				$navbar = 'navbar-default';

		}

		return ( parent::title( $navbar));

	}

	protected function openContent() {
		if ( $this->contentOPEN )
			return ( $this);

		$this->contextmenu();

		if ( \config::$CSS_BASE == 'mini') {
			$this->closeContentTags[] = '	</div></div><!-- /_page:Main Content Area -->' . PHP_EOL;
			print <<<OUTPUT

	<div class="main-content-wrapper"><div class="row"><!-- _page:Main Content Area -->

OUTPUT;

		}
		else {
			$this->closeContentTags[] = '	</div><!-- /_page:Main Content Area -->' . PHP_EOL;
			print <<<OUTPUT

	<div class="main-content-wrapper"><!-- _page:Main Content Area -->

OUTPUT;

		}

		$this->contentOPEN = TRUE;

		return ( $this);

	}

	public function content() {
		$this
			->header()
			->closeSection()
			->openContent()
			->section( 'content', 'content', 'content');

		return ( $this);	// chain

	}

	public function primary( $class = NULL) {
		if ( is_null( $class)) {
			if ( \config::$CSS_BASE == 'mini')
				$class = 'col-sm-12 col-md-9';
			else
				$class = 'content-primary';

		}

		$this->closeSection();
		$this->openContent();
		$this->sectionOPEN = TRUE;
		$this->sectionNAME = 'content-primary';
		printf( '		<div class="%s" data-role="content-primary">%s', $class, PHP_EOL );

		return ( $this);	// chain

	}

	public function secondary( $class= NULL) {
		if ( is_null( $class)) {
			if ( \config::$CSS_BASE == 'mini')
				$class = 'col-sm-12 col-md-3';
			else
				$class = 'content-secondary';

		}

		$this->closeSection();
		$this->openContent();
		$this->sectionOPEN = TRUE;
		$this->sectionNAME = 'content-secondary';
		printf( '		<div class="%s" data-role="content-secondary">%s', $class, PHP_EOL );

		return ( $this);	// chain

	}

	public function pagefooter() {
		$this
			->header()
			->pageHeader()
			->closeSection()
			->closeContent();

		$v = new \view;
		if ( \config::$CSS_BASE == 'mini')
			$v->load( 'footer-mini');
		else
			$v->load( 'footer');

		return ( $this);	// chain

	}

	public function menu() {}
	public function contextmenu() {}

}
