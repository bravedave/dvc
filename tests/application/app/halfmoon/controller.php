<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * styleguide : https://codeguide.co/
*/

namespace halfmoon;

use Json;
use sys;

class controller extends \Controller {
  protected function _index() {
		$this->render([
			'primary' => 'blank',
			'secondary' => [
				'index'

			]

		]);

  }

	protected function getView( $viewName = 'index', $controller = null, $logMissingView = true) {
		$view = sprintf( '%s/views/%s.php', __DIR__, $viewName );		// php
		if ( file_exists( $view))
      return ( $view);

    // \sys::logger( sprintf('<%s> %s', $viewName, __METHOD__));

		return parent::getView( $viewName, $controller, $logMissingView);

	}

	protected function render( $params) {
		$defaults = [
      'left-interface' => true,
			'footer' => __DIR__ . '/views/footer',
			'navbar' => __DIR__ . '/views/navbar',
			'template' => 'halfmoon\page',

    ];

    $options = array_merge( $defaults, $params);

    return parent::render( $options);

  }

  function assets( $type = 'js') {
    if ( 'css' == $type) {
      sys::serve( __DIR__ . '/lib/css/halfmoon.min.css');

    }
    elseif ( 'js' == $type) {
      sys::serve( __DIR__ . '/lib/js/halfmoon.min.js');

    }

  }

}
