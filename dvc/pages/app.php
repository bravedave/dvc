<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/
NameSpace dvc\pages;

class app extends _page {
	public $headerTemplate = 'app-header';
	public $footerTemplate = 'footer';

	function __construct( $title = '' ) {
		parent::__construct( $title);
		$this->meta[] = '<meta name="viewport" content="width=device-width, initial-scale=1" />';
		$this->meta[] = '<meta name="apple-mobile-web-app-capable" content="yes" />';

		$aCss = array( 'custom');
		if ( \application::app())
			$aCss[] = \application::app()->controller();

		foreach ( $aCss as $cssFile) {
			if ( file_exists( realpath( '.' ) . '/css/' . $cssFile . '.css' ))
				$this->css[] = sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%scss/%s.css" />', \url::$URL, $cssFile );
			elseif ( file_exists( \application::app()->getRootPath() . '/app/public/css/' . $cssFile . '.css' ))
				$this->css[] = sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%scss/%s.css" />', \url::$URL, $cssFile );

		}

	}

	public function pageHeader() {
		if ( $this->boolpageHeader )
			return ( $this);

		parent::pageHeader();

		$v = new \view;
			$v->title = $this->title;
			$v->load( $this->headerTemplate);

		return ( $this);

	}

	public function pagefooter() {
		$this
			->header()
			->pageHeader();

		$v = new \view;
		$v->load( $this->footerTemplate);

		return ( $this);	// chain

	}

}
