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

	protected function postHandler() {
		$action = $this->getPost('action');
		if ( $action == 'get-template') {
			$template = $this->getPost( 'template');
			sys::getTemplate( $template);

		}

	}

	public function index( $data = '' ) {
		//~ if ( !config::checkDBconfigured())
			//~ Response::redirect( url::$URL . 'install/db' );

		if ( $this->isPost()) {
			$this->postHandler();

		}
		else {
			if ( $data == '') {
				// just points into the documentation
				$p = new dvc\pages\bootstrap;
					$p
						->header()
						->title();

					$this->load( 'fork-me');

					$p->secondary();
						$this->load('contents');

					$p->primary();
						$this->load( 'index');


			}
			else {
				parent::index();	// $this->load('not-found');

			}

		}

	}

}
