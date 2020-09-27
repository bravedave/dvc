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

    $this->data = (object)[
      'title' => 'WebApp'

    ];

    if ( in_array( $view, $views)) {
      $this->load( $view);

    }
    elseif ( 'css' == $view) {
      \sys::serve( __DIR__ . '/assets/custom.css');

    }
    else {
      $this->render([
        'title' => 'WebApp',
        'content' => 'index',

      ]);

    }

  }

  protected function render( $params) {
    $defaults = [
      'template' => \config::$PAGE_TEMPLATE_APP,
      'css' => (array)sprintf( '<link rel="stylesheet" href="%s">', strings::url( $this->route . '/css')),
      'navbar' => __DIR__ . '/views/navbar',
      'footer' => __DIR__ . '/views/footer'

    ];

    $options = array_merge( $defaults, $params);

    return parent::render( $options);

  }

}
