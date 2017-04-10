<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/
class material extends Controller {
	public function index() {
		$p = new materializecss( $this->title = config::$WEBNAME . '::auto-complete-test');
			$p->header();
			$this->loadView('materialize');

	}

	public function autocomplete() {
		$p = new materializecss( $this->title = config::$WEBNAME . '::auto-complete-test');
			$p->header();
			$this->loadView('auto-complete-test');

	}

}

