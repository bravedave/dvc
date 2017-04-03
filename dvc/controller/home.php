<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/

class home extends Controller {
	public $RequireValidation = FALSE;

	public function index( $data = '' ) {
		//~ if ( !config::checkDBconfigured())
			//~ Response::redirect( url::$URL . 'install/db' );

		if ( $data == '') {
			$p = new Page();
				$p->header();
				$p->title();

				//~ $p->content();
				//~ print 'content';

				$p->secondary();
				print 'secondary';

				$p->primary();
				print 'primary';

		}
		else {
			$this->page404();

		}

	}

}