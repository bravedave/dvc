<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * DO NOT change this file
 * Copy it to <application>/app/dvc/ and modify it there
 *
*/

namespace halfmoon;

use dvc;
use strings;

class page extends dvc\pages\_page {
	protected function _pagefooter() {
    if ( $this->debug) \sys::logger( sprintf('<%s> %s', '', __METHOD__));

		return $this
			->header()
			->pageHeader()
			->closeSection();

	}

	function __construct( $title = '' ) {
    // $this->debug = true;
    $this->dvc = '4';
    parent::__construct( $title);

    $this->css = [];

    $css = strings::url( 'halfmoon/assets/css');
    $this->css[] = sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', $css);
    $css = strings::url( '/css/font-awesome.min.css');
    $this->css[] = sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', $css);


    $js = strings::url( 'halfmoon/assets/js');
    $this->latescripts[] = sprintf( '<script type="text/javascript" src="%s"></script>', $js);

    $this->meta[] = '<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />';
    $this->meta[] = '<meta name="viewport" content="width=device-width" />';

  }

	public function close() {
		$this
			->header()
			->pageHeader()
			->closeSection();

		$time = '';
		if ( $this->boolOpen ) {
			if ( $this->footer )
        $this->pagefooter();

    }

		$this->closeContent();

		if ( $this->boolOpen ) {
			foreach ( $this->closeTags as $tag )
				print $tag;

			$this->closeTags = [];

			foreach ( $this->latescripts as $script )
				print "\t" . $script . PHP_EOL;

			printf( '%s</body>%s</html>%s', PHP_EOL, PHP_EOL, PHP_EOL );

		}

		$this->boolOpen = FALSE;

  }

	protected function openContent() {
		if ( $this->contentOPEN )
			return ( $this);

		$this->closeContentTags[] = '	</div><!-- /_page:Main Content Area -->' . PHP_EOL;
		$classes = [
      'page-wrapper',
      'with-navbar',
      'with-sidebar'

    ];

    if ( $this->footer) {
      $classes[] = 'with-navbar-fixed-bottom';

    }

		printf( '%s%s	<div class="%s" data-role="main-content-wrapper"><!-- _page:Main Content Area -->%s', PHP_EOL, PHP_EOL, implode( ' ', $classes), PHP_EOL);

		$this->contentOPEN = true;

		return ( $this);

	}

	public function pageHeader() {
    if ( $this->boolpageHeader )
    return ( $this);

		$this->boolpageHeader = true;

		$this->closeHeader();

    print '<body class="with-custom-webkit-scrollbars with-custom-css-scrollbars" data-dm-shortcut-enabled="true" data-sidebar-shortcut-enabled="true" data-set-preferred-theme-onload="true">' . PHP_EOL;

		return ( $this);

	}

	public function primary( $class = null, $more = null) {
		if ( is_null( $class)) {
			$class = 'content-wrapper';

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
			$class = 'sidebar';

		}

		$this
			->header()
			->closeSection()
			->openContent()
			->section( 'content-secondary', $class, 'content-secondary', $more);

		return ( $this);	// chain

  }

	public function title( $navbar = 'navbar-default') {

    if ( $this->debug) \sys::logger( sprintf('<%s> %s', $navbar, __METHOD__));

    if ( $navbar) {

      $this
        ->header()
        ->pageHeader()
        ->closeSection()
        ->openContent();
        // ->section( 'navbar', '', 'navbar');

      $v = new \view( $this->data);
        $v->title = $this->title;
        foreach( (array)$navbar as $_){
          $v->load( $_);

        }

    }

		return ( $this);	// chain

  }

}

