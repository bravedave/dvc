<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	example using vue.js

	*/

class v extends Controller {
	protected function vanilla() {
		$p = new dvc\pages\vuejs;
			$p
				->header()
				->title();

		$this->load( 'vue-js');


	}

	function index() {
		$this->vanilla();

	}

}
