<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
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
		else {
			parent::postHandler();

		}

	}

	protected function _index( $data = '' ) {
		// just points into the documentation

		$readme = implode( DIRECTORY_SEPARATOR, [
			dirname( dirname( dirname( __DIR__))),
			'Readme.md'

		]);

		// \sys::logger( sprintf('<%s> %s', $readme, __METHOD__));

		$primary = [ $readme];
		$secondary = ['docs/contents'];
		$sample = implode( DIRECTORY_SEPARATOR, [ $this->rootPath, 'controller', 'hello.php' ]);
		if ( file_exists( $sample)) {
			$primary[] = 'docs/sample';
			$secondary[] = 'docs/sample-index';

		}

		$this->render([
			'title' => $this->title,
			'primary' => $primary,
			'secondary' => $secondary

		]);

	}

	public function index( $data = '') {

		/*
			if you set this you will get some stats in the system log
			about how many loads have occurrered	*/
		//~ sys::loaderCounter( new dvc\hitter('total loads'));
		parent::index( $data);

	}

}
