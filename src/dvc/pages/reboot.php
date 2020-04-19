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

use sys;

class reboot extends _page {
	public function __construct( $title = '' ) {
        parent::__construct( $title);

    }

	protected function openContent() {
		if ( $this->contentOPEN )
            return ( $this);

		$this->closeContentTags[] = '	</div></div><!-- /_page:Main Content Area -->' . PHP_EOL;

        printf( '	<div class="container-fluid"><div class="row"><!-- _page:Main Content Area -->%s', PHP_EOL);

		return parent::openContent();

	}

	static $contentClass = 'col pt-3 pb-4';
	static $primaryClass = 'col-sm-8 col-md-9 pt-3 pb-4';
	static $secondaryClass = 'col-sm-4 col-md-3 pt-3 pb-4 d-print-none';

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
