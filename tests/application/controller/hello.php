<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

class hello extends Controller {
	protected function posthandler() {
		$action = $this->getPost('action');

		if ( 'gibblegok' == $action) {
			\Json::ack( $action);

		}
		else {
			parent::postHandler();

		}

	}

	protected function _index() {
		$this->render([
			'title' => 'hello world',
			'primary' => 'hello',
			'secondary' => ['index', 'tests/index']
		]);

	}

}
