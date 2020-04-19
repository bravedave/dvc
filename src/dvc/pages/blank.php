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

class blank extends _page {
	function __construct( $title = '' ) {
		parent::__construct( $title );

		$this->latescripts = [];
		$this->css = [
			sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', \url::tostring( 'css/min.css' ))

		];

	}

	public function pagefooter() {}

}
