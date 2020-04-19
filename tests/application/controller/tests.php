<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

class tests extends Controller {
	protected function posthandler() {
		$action = $this->getPost('action');

		if ( 'gibblegok' == $action) {
			\Json::ack( $action);

		}
		else {
			parent::postHandler();

		}

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
		throw new Exceptions\TestException( 'test');

	}

}
