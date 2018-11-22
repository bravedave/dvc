<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	DO NOT change this file
	Copy it to <application>/app/dvc/ and modify it there
	*/
namespace dvc\pages;

class bootstrap extends page {

	static $SCALE = 1;

	static $pageContainer = 'container-fluid';
	static $contentClass = 'col pt-3 pb-4';
	static $primaryClass = 'col-sm-8 col-md-9 pt-3 pb-4';
	static $secondaryClass = 'col-sm-4 col-md-3 pt-3 pb-4 bg-light d-print-none';

	function __construct( $title = '' ) {
		parent::$pageContainer = self::$pageContainer;
		self::$BootStrap = true;
		if ( \config::$BOOTSTRAP_VERSION == '3') {
			$this->jQuery2 = true;

		}
		elseif ( \config::$BOOTSTRAP_VERSION == '4' && $this->dvc) {
			$this->dvc = '4';

		}

		parent::__construct( $title );

		$this->meta[] = sprintf('<meta name="viewport" content="width=device-width, initial-scale=%s, shrink-to-fit=no" />', self::$SCALE);

		if ( \config::$BOOTSTRAP_VERSION == '3') {
			$css = \url::tostring( 'bootstrap.3/css/bootstrap.min.css');
			$js = \url::tostring( 'bootstrap.3/js/bootstrap.min.js');

			array_unshift( $this->css, sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', $css));

			$this->latescripts[] = sprintf( '<script type="text/javascript" src="%s"></script>', $js);

		}
		elseif ( \config::$BOOTSTRAP_VERSION == '4') {
			$css = \url::tostring( 'assets/bootstrap/css');
			$js = \url::tostring( 'assets/bootstrap/js');

			array_unshift( $this->css, sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', $css));

			$this->latescripts[] = sprintf( '<script type="text/javascript" src="%s"></script>', $js);

		}
		else { throw new \Exceptions\InvalidBootstrapVersion; }

	}

	public function content( $class = null, $more = null) {
		if ( is_null( $class))
			$class = self::$contentClass;

		return ( parent::content( $class, $more));	// chain

	}

	public function primary( $class = null, $more = null) {
		if ( is_null( $class))
			$class = self::$primaryClass;

		return ( parent::primary( $class, $more));	// chain

	}

	public function secondary( $class = null, $more = null) {
		if ( is_null( $class))
			$class =  self::$secondaryClass;

		return ( parent::secondary( $class, $more));	// chain

	}

}
