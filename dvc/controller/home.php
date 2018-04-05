<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/

class home extends Controller {
	public $RequireValidation = \config::lockdown;

	protected function postHandler() {
		$action = $this->getPost('action');
		if ( $action == 'get-template') {
			$template = $this->getPost( 'template');
			//~ sys::logger( sprintf( 'template request : %s', $template));
			sys::getTemplate( $template);

		}
		elseif ( $action == 'get-vue-block') {
			$block = $this->getPost( 'block');
			// sys::logger( sprintf( 'home :: get-vue-block request : %s', $block));
			vue::getBlock( $block);

		}

	}

	protected function _index( $data = '' ) {
		// just points into the documentation
		if ( $data == '') {
			$this->render([
				'title' => $this->title,
				'primary' => 'index',
				'secondary' => ['contents']]);

		}
		else {
			parent::index();

		}

	}

	public function index( $data = '') {
		//~ if ( !config::checkDBconfigured())
			//~ Response::redirect( url::$URL . 'install/db' );

		/*
			if you set this you will get some stats in the system log
			about how many loads have occurrered	*/
		//~ sys::loaderCounter( new dvc\hitter('total loads'));

		$this->isPost() ?
			$this->postHandler() :
			$this->_index( $data);


	}

}
