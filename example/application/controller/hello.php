<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/

class hello extends Controller {
	function index() {
		$p = new Page();
			$p
				->header()
				->title();

			$p->primary();
				print 'hello<br />';
				print dvc\html::icon( 'John Citizen');

			$p->secondary();
				print 'secondary';

	}

}
