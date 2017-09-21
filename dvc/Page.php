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
NameSpace dvc;

class Page extends pages\page {
	protected $bootstrap,
		$datepicker = FALSE;

	function __construct( $title = '' ) {
		parent::__construct( $title );

		$this->bootstrap = config::$BOOTSTRAP;

		if ( $this->bootstrap) {
			if ( $this->datepicker) {
				array_unshift( $this->css, sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', url::tostring( 'css/bootstrap-datepicker.min.css' )));
				$this->latescripts[] = sprintf( '<script type="text/javascript" src="%s"></script>', url::tostring( 'js/bootstrap-datepicker.min.js' ));

			}

			array_unshift( $this->css, sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', url::tostring( 'css/bootstrap.min.css' )));
			$this->latescripts[] = sprintf( '<script type="text/javascript" src="%s"></script>', url::tostring( 'js/bootstrap.min.js' ));

		}

	}

	public function title( $navbar =  'navbar-inverse') {
		parent::title( $navbar);
		return ( $this);

	}

}
