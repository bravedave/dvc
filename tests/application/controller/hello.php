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
      'secondary' => [
        'index-hello',
        'index'
      ]

		]);

  }

  public function convert() {
    $file = implode( DIRECTORY_SEPARATOR, [
      __DIR__,
      '..',
      'data',
      'sample.html'

    ]);

    Response::text_headers();
    if ( file_exists( $file)) {
      print strings::html2text( file_get_contents( $file));

    }
    else {
      print 'not found';

    }

  }

}
