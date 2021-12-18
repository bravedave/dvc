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
    $this->render([
      'secondary' => ['aside'],
      'primary' => ['main']
    ]);
  }

  protected function before() {
    parent::before();
  }

  protected function postHandler() {
    $action = $this->getPost('action');
    parent::postHandler();
  }
}
