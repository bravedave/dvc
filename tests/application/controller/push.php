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

class push extends Controller {
	protected function _index() {
    \sys::logger( sprintf('<%s> %s', application::app()->getRootPath(), __METHOD__));

		$this->render([
      'footer' => implode( DIRECTORY_SEPARATOR, [
        application::app()->getRootPath(),
        'app',
        'views',
        'push',
        'footer'

      ]),
			'title' => 'Push Notifications',
			'primary' => 'about',
			'secondary' => ['index']

		]);

  }

}
