<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace webapp;

use strings;

class controller extends \Controller {
  protected $viewPath = __DIR__ . '/views';

  protected function _index( $view = '') {
    $views = [
      'nav',
      'footer',
      'content',
      'content2',

    ];

    if ( in_array( $view, $views)) {
      $this->load( $view);

    }
    elseif ( 'css' == $view) {
      \sys::serve( __DIR__ . '/views/custom.css');

    }
    else {
      $this->render([
        'content' => 'index'

      ]);

    }

  }

  protected function render( $params) {
    $defaults = [
      'template' => \config::$PAGE_TEMPLATE_APP,
      'css' => (array)sprintf( '<link rel="stylesheet" href="%s">', strings::url( $this->route . '/css')),

    ];

    $options = array_merge( $defaults, $params);

    return parent::render( $options);

  }

}
