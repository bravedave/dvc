<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/

class docs extends Controller {
	public $RequireValidation = FALSE;

	public function index( $view = 'index') {
		if ( !$view)
			$view = 'index';

		$p = new Page();
			$p
				->header()
				->title();

			$p->secondary();
				$this->load('contents');

			$p->primary();
				$this->load( (string)$view);

	}

}
