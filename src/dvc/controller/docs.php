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
		//~ $this->debug = true;

		$contents = 'contents';
		if ( $this->hasView( $_c = sprintf( '%s-contents', $view))) {
			$contents = $_c;

		}
		else if ( strpos( $view, '-') !== false) {
			//~ die( $view);
			//~ die( sprintf( '%s-contents', preg_replace( '/-.*/', '', $view)));

			if ( $this->hasView( $_c = sprintf( '%s-contents', preg_replace( '/-.*/', '', $view)))) {
				$contents = $_c;

			}

		}

		$this->render([
			'title' => $this->title = sprintf( 'Docs - %s', ucwords( $view)),
			'primary' => [(string)$view, 'docs-format'],
			'secondary' => $contents,

		]);

	}

}
