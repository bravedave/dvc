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
NameSpace dvc\pages;

class bootstrap extends page {
	static public $BootStrap_Version = '3';

	function __construct( $title = '' ) {
		self::$BootStrap = TRUE;
		if ( self::$BootStrap_Version == '4')
			$this->dvc = '4';

		parent::__construct( $title );

		if ( self::$BootStrap_Version == '3' || self::$BootStrap_Version == '4') {
			$css = [ \url::tostring( sprintf( 'bootstrap.%s/css/bootstrap.min.css', self::$BootStrap_Version))];
			$js = [\url::tostring( sprintf( 'bootstrap.%s/js/bootstrap.min.js', self::$BootStrap_Version))];
			if ( self::$BootStrap_Version == '4')
				array_unshift( $js, \url::tostring( sprintf( 'bootstrap.%s/js/popper.min.js', self::$BootStrap_Version)));

			foreach ( $css as $c)
				array_unshift( $this->css, sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', $c));

			foreach ( $js as $j)
				$this->latescripts[] = sprintf( '<script type="text/javascript" src="%s"></script>', $j);

		}
		else {
			throw new \Exception( 'invalid bootstrap version');

		}

	}

	//~ public function title( $navbar =  'navbar-inverse') {
		//~ parent::title( $navbar);
		//~ return ( $this);

	//~ }

	public function primary( $class = NULL) {
		if ( is_null( $class))
			$class =  'col-sm-8 col-md-9 py-3';

		return ( parent::primary( $class));	// chain

	}

	public function secondary( $class = NULL) {
		if ( is_null( $class))
			$class =  'col-sm-4 col-md-3 py-4';

		return ( parent::secondary( $class));	// chain

	}

}
