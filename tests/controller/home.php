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
  protected $viewPath = __DIR__ . '/views/';

  protected function _index() {
    if ('4' == config::$BOOTSTRAP_VERSION) {
      $this->render([
        'secondary' => ['aside'],
        'navbar' => 'navbar-4',
        'primary' => ['main']
      ]);
    } else {
      \sys::logger(sprintf('<%s> %s', config::$PAGE_TEMPLATE, __METHOD__));
      $this->render([
        'secondary' => ['aside'],
        'primary' => ['main']
      ]);
    }
  }

  protected function before() {
    parent::before();
  }

  protected function postHandler() {
    $action = $this->getPost('action');
    parent::postHandler();
  }

  public function tiny() {
    if ('4' == config::$BOOTSTRAP_VERSION) {
      $this->render([
        'secondary' => ['aside'],
        'navbar' => 'navbar-4',
        'primary' => ['tiny']
      ]);
    } else {
      \sys::logger(sprintf('<%s> %s', config::$PAGE_TEMPLATE, __METHOD__));
      $this->render([
        'secondary' => ['aside'],
        'primary' => ['main']
      ]);
    }
  }
}
