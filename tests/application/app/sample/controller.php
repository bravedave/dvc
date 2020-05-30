<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace sample;

class controller extends \Controller {
    protected function _index() {
		$this->render([
			'primary' => 'default',
			'secondary' => [
				'index',
				'index-modal',

			]

		]);

	}

	protected function getView( $viewName = 'index', $controller = null, $logMissingView = true) {
		$view = sprintf( '%s/views/%s.php', __DIR__, $viewName );		// php
		if ( file_exists( $view))
			return ( $view);

		return parent::getView( $viewName, $controller, $logMissingView);

	}

	protected function posthandler() {
		$action = $this->getPost('action');

		if ( 'gibblegok' == $action) {
			\Json::ack( $action);

		}
		else {
			parent::postHandler();

		}

	}

	public function changes() {
		$this->render([
			'title' => 'Change Log',
			'primary' => 'changes',
			'secondary' => 'index'

		]);

	}

	public function info() {
		/* default setting
		* in case you forget to disable this on a production server
		* - only running on localhost
		*/
		if ( $this->Request->ServerIsLocal()) {
			$this->render([
				'title' => 'PHP Info',
				'primary' => 'info',
				'secondary' => 'index'

				]

			);

		}

	}

	public function errTest() {
		throw new \Exceptions\TestException( 'test');

	}

}