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

class reboot extends _core {
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

}
