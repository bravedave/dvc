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
	protected $boolHeader = FALSE;
	protected $headerOPEN = FALSE;
	protected $contentOPEN = FALSE;
	protected $sectionOPEN = FALSE;
	protected $sectionNAME = '';

	public $title = '';
	public $charset = FALSE;

	public $meta = array(),
		$scripts = array(),
		$latescripts = array(),
		$css = array(),
		$closeTags = array(),
		$closeContentTags = array(),
		$footer = TRUE,
		$bodyClass = FALSE;

	function __construct( $title = '' ) {
		$this->title = ( $title == '' ? \config::$WEBNAME : $title );

		$this->meta[] = '<meta http-equiv="Content-Language" content="en" />';

		$this->scripts[] = sprintf( '<script type="text/javascript" src="%s"></script>', \url::$URL . 'js/jquery-2.1.1.min.js' );

		if ( \jslib::brayworth())
			$this->latescripts[] = sprintf( '<script type="text/javascript" src="%s"></script>', \jslib::$brayworthlib );
		else
			$this->latescripts[] = sprintf( '<script type="text/javascript" src="%sjs/brayworth.js"></script>', \url::$URL);


		$this->css[] = sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', \url::$URL . 'css/font-awesome.min.css' );
		if ( \cssmin::dvc())
			$this->css[] = sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', \cssmin::$dvcmin );
		else
			$this->css[] = sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%scss/dvc.css" />', \url::$URL );

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

		if ( !$this->charset)
			$this->charset = 'utf-8';

		print <<<OUTPUT

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=$this->charset" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title>$this->title</title>

OUTPUT;

		foreach ( $this->meta as $meta )
			print "\t" . $meta . PHP_EOL;

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

			print <<<OUTPUT

</head>

OUTPUT;

		}

		return ( $this);

	}

	public function pageHeader() {
		$this->closeHeader();

		if ( $this->bodyClass)
			printf( '<body class="%s">%s', $this->bodyClass, PHP_EOL);

		else
			print '<body>' . PHP_EOL;

		return ( $this);

	}

	protected function openContent() {
		if ( $this->contentOPEN )
			return ( $this);

		$this->closeContentTags[] = '	</div><!-- /_page:Main Content Area -->' . PHP_EOL;
		print <<<OUTPUT

	<div class="main-content-wrapper"><!-- _page:Main Content Area -->

OUTPUT;

		$this->contentOPEN = TRUE;

		return ( $this);

	}

	protected function closeContent() {
		if ( $this->contentOPEN ) {
			foreach ( $this->closeContentTags as $tag )
				print $tag;

			$this->contentOPEN = FALSE;

		}

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

	public function pagefooter() {
		$v = new \view;
		$v->load( 'footer');

		return ( $this);	// chain

	}

	public function close() {
		$this->closeSection();
		$this->closeContent();

		$time = '';
		if ( $this->boolOpen ) {
			if ( $this->footer )
				$this->pagefooter();

			foreach ( $this->closeTags as $tag )
				print $tag;

			$this->closeTags = array();

			foreach ( $this->latescripts as $script )
				print "\t" . $script . PHP_EOL;

			printf( '%s</body>%s</html>%s', PHP_EOL, PHP_EOL, PHP_EOL );

		}

		$this->boolOpen = FALSE;

	}

}
