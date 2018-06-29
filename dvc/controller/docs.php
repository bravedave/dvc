<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	DO NOT change this file
	Copy it to <application>/app/views/ and modify it there
	*/

class docs extends Controller {
	public $RequireValidation = \config::lockdown;

	public function index( $view = 'index') {
		if ( !$view) {
			$view = 'index';

		}
		// $p = new dvc\pages\bootstrap4;
		// 	$p
		// 		->header()
		// 		->title();

		// $this->load( 'fork-me');

		// $p->secondary();
		// 	$this->load('contents');

		// $p->primary();
		// 	$this->load( (string)$view);
		$this->render([
			'title' => 'hello world',
			'primary' => (string)$view,
			'secondary' =>'contents'
		]);

	}

}
