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

class bootstrap extends page {

	static $contentClass = 'col pt-3 pb-4';
	static $primaryClass = 'col-sm-8 col-md-9 pt-3 pb-4';
	static $secondaryClass = 'col-sm-4 col-md-3 pt-3 pb-4 d-print-none';

	function __construct( $title = '' ) {
		// \sys::logger( sprintf( 'Bootstrap_Version : bootstrap : %s', self::$Bootstrap_Version));

		parent::$pageContainer = self::$pageContainer;
		self::$BootStrap = true;
		if ( self::$Bootstrap_Version == '3') {
			$this->jQuery2 = true;

		}
		elseif ( self::$Bootstrap_Version == '4' && $this->dvc) {
			$this->dvc = '4';

		}

		parent::__construct( $title );
		if ( !self::$pageContainer) {
			self::$pageContainer = 'container-fluid';

		}

		if ( self::$Bootstrap_Version == '3') {
			$css = \url::tostring( 'bootstrap.3/css/bootstrap.min.css');
			$js = \url::tostring( 'bootstrap.3/js/bootstrap.min.js');

			array_unshift( $this->css, sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', $css));

			$this->latescripts[] = sprintf( '<script type="text/javascript" src="%s"></script>', $js);

		}
		elseif ( self::$Bootstrap_Version == '4') {
			$css = \url::tostring( 'assets/bootstrap/css');
			$js = \url::tostring( 'assets/bootstrap/js');

			array_unshift( $this->css, sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', $css));

			$this->latescripts[] = sprintf( '<script type="text/javascript" src="%s"></script>', $js);

		}
		else { throw new \Exceptions\InvalidBootstrapVersion; }

	}

	public function content( $class = null, $more = '') {
		return ( parent::content( is_null( $class) ? self::$contentClass : $class, $more));	// chain

	}

	public function primary( $class = null, $more = '') {
		return ( parent::primary( is_null( $class) ? self::$primaryClass : $class, $more));	// chain

	}

	public function secondary( $class = null, $more = '') {
		return ( parent::secondary( is_null( $class) ? self::$secondaryClass : $class, $more));	// chain

	}

}
