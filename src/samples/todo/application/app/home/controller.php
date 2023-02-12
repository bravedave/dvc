<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace home;

use bravedave\dvc\json;
use config, strings;

class controller extends \Controller {

  protected function _index() {

    config::$BOOTSTRAP_VERSION = 5;

    $this->data = (object)[
      'title' => $this->title = config::$WEBNAME,
      'pageUrl' => strings::url($this->route),
      'searchFocus' => true
    ];

    $this->renderBS5([
      'navbar' => fn () => $this->load('navbar'),
      'main' => fn () => $this->load('matrix'),
      'aside' => false
    ]);
  }

  protected function before() {

    config::checkdatabase();

    parent::before();
    $this->viewPath[] = __DIR__ . '/views/';
  }

  protected function postHandler() {
    $action = $this->getPost('action');

    if ('get-todo-data' == $action) {

      json::ack($action)
        ->add('data', (new \dao\todo)->getMatrix());
    } elseif ('todo-add' == $action) {

      (new \dao\todo)->Insert([
        'description' => $this->getPost('description')
      ]);

      json::ack($action);
    } elseif ('todo-delete' == $action) {

      if ($id = (int)$this->getPost('id')) {

        (new \dao\todo)->delete($id);
        json::ack($action);
      } else {

        json::nak($action);
      }
    } else {

      parent::postHandler();
    }
  }
}
