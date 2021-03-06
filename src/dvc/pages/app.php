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

class app extends _page {
	public $headerTemplate = 'app-header';
	static $footerTemplate = 'footer';

	static $webAppCapable = TRUE;

	function __construct( $title = '' ) {
		parent::__construct( $title);
		$this->meta[] = '<meta name="viewport" content="width=device-width, initial-scale=1" />';
		if ( self::$webAppCapable)
			$this->meta[] = '<meta name="apple-mobile-web-app-capable" content="yes" />';

		$aCss = [ 'custom'];
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
		$v->load( self::$footerTemplate);

		return ( $this);	// chain

	}

}
