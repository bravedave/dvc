<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/

class x extends Controller {
	function index() {
		$this->hello();

	}

	function hello() {
		$p = new Page();
			$p->header();
			$p->title();

			//~ $p->content();
			//~ print 'content';

			$p->primary();
				//~ print 'primary';
				print 'hello<br />';
				print dvc\html::icon( 'John Doe');

			$p->secondary();
				print 'secondary';

	}

}
