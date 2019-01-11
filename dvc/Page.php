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
namespace dvc;

class Page extends pages\page {
	protected $bootstrap,
		$datepicker = FALSE;

	function __construct( $title = '' ) {
		parent::__construct( $title );

		$this->bootstrap = config::$BOOTSTRAP;

		if ( $this->bootstrap) {
			$css = [ \url::tostring( 'bootstrap.3/css/bootstrap.min.css')];
			$js = [\url::tostring( 'bootstrap.3/js/bootstrap.min.js')];
			if ( $this->datepicker) {
				$css[] = \url::tostring( 'bootstrap.3/css/bootstrap-datepicker.min.css');
				$js[] = \url::tostring( 'bootstrap.3/js/bootstrap-datepicker.min.js');

			}

			foreach ( $css as $c)
				array_unshift( $this->css, sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', $c));

			foreach ( $js as $j)
				$this->latescripts[] = sprintf( '<script type="text/javascript" src="%s"></script>', $j);

		}

	}

	public function title( $navbar =  'navbar-inverse') {
		return ( parent::title( $navbar));

	}

}
