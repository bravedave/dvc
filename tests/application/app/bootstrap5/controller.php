<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bootstrap5;

use Json;

class controller extends \Controller {
  protected $viewPath = __DIR__ . '/views';

	protected function posthandler() {
		$action = $this->getPost('action');

		if ( 'gibblegok' == $action) {
			Json::ack( $action);

		}
		else {
			parent::postHandler();

		}

	}

	protected function _index() {
		$this->render([
			'title' => 'bootstrap5',
			'primary' => 'blank',
      'secondary' => [
        'index',
        'index-modal'
      ]

		]);

  }

	protected function page( $params) {
		$defaults = [
			'templatex' => '\dvc\pages\bootstrap5'

		];

    $options = array_merge( $defaults, $params);

    return parent::page( $options);

  }

}
