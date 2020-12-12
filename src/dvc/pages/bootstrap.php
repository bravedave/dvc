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

use strings;

class bootstrap extends page {

	static $SCALE = 1;

	static $contentClass = 'col pt-3 pb-4';
	static $primaryClass = 'col-md-9 pt-3 pb-4';
	static $secondaryClass = 'col-md-3 pt-3 pb-4 d-print-none';

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
		elseif ( self::$Bootstrap_Version == '5' && $this->dvc) {
			$this->dvc = '4';

		}

		parent::__construct( $title );
		if ( !self::$pageContainer) {
			self::$pageContainer = 'container-fluid pb-2';

		}

		$this->meta[] = sprintf('<meta name="viewport" content="width=device-width, initial-scale=%s, shrink-to-fit=no" />', self::$SCALE);

		if ( self::$Bootstrap_Version == '3') {
			$css = strings::url( 'bootstrap.3/css/bootstrap.min.css');
			$js = strings::url( 'bootstrap.3/js/bootstrap.min.js');

			array_unshift( $this->css, sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', $css));

			$this->latescripts[] = sprintf( '<script type="text/javascript" src="%s"></script>', $js);

		}
		elseif ( self::$Bootstrap_Version == '4') {
			$css = strings::url( 'assets/bootstrap/css');
			$icons = strings::url( 'assets/bootstrap/icons');
			$js = strings::url( 'assets/bootstrap/js');

			array_unshift( $this->css, sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', $icons));
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
