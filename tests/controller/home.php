<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

use bravedave\dvc\{controller, json, logger, Response, ServerRequest};

class home extends controller {

  protected function _index() {

    user::uid();

    if ('4' == config::$BOOTSTRAP_VERSION) {

      $this->render([
        'secondary' => ['aside'],
        'navbar' => 'navbar-4',
        'primary' => ['main']
      ]);
    } else {

      $this->data = (object)[
        'title' => $this->title = config::$WEBNAME,
        'pageUrl' => strings::url($this->route),
        'searchFocus' => true,
        'aside' => ['aside']
      ];

      $this->data = (object)[
        'title' => $this->title = config::$WEBNAME,
        'pageUrl' => strings::url($this->route),
        'searchFocus' => true,
        'aside' => ['aside']
      ];

      $main = 'main';
      $main = 'todo-matrix';

      // echo $bullshit;  // which is an error

      $this->renderBS5([
        'main' => fn() => $this->load($main)
      ]);
    }
  }

  protected function before() {

    config::checkdatabase();
    parent::before();
    // $this->viewPath[] = __DIR__ . '/views/';
  }

  protected function postHandler() {

    $request = new ServerRequest;
    $action = $request('action');

    match ($action) {
      'get-todo-data' => handler::getTodoData($request),
      'todo-add' => handler::todoAdd($request),
      'todo-delete' => handler::todoDelete($request),
      'todo-update' => handler::todoUpdate($request),
      'hello' => json::ack($action),
      default => parent::postHandler()
    };
  }

  public function accordion() {

    if ('4' == config::$BOOTSTRAP_VERSION) {
      $this->render([
        'secondary' => ['aside'],
        'navbar' => 'navbar-4',
        'primary' => ['accordion-php']
      ]);
    } else {
      $this->render([
        'secondary' => ['aside'],
        'primary' => ['accordion-php']
      ]);
    }
  }

  public function info() {

    phpinfo();
  }

  public function squire() {

    $this->data = (object)[
      'title' => $this->title = config::$WEBNAME,
      'pageUrl' => strings::url($this->route),
      'searchFocus' => true,
      'aside' => ['aside']
    ];

    $this->renderBS5([
      'main' => fn() => $this->load('squire'),
      'scripts' => [
        '<script type="text/javascript" src="dist/purify.min.js"></script>',
        '<script type="text/javascript" src="dist/squire.js"></script>'
      ]
    ]);
  }

  public function toast() {

    $this->data = (object)[
      'title' => $this->title = config::$WEBNAME,
      'pageUrl' => strings::url($this->route),
      'searchFocus' => true,
      'aside' => ['aside']
    ];

    $this->renderBS5([
      'main' => fn() => $this->load('toast'),
      'toastui' => true
    ]);
  }

  public function tiny() {

    $this->data = (object)[
      'title' => $this->title = config::$WEBNAME,
      'pageUrl' => strings::url($this->route),
      'searchFocus' => true,
      'aside' => ['aside']
    ];

    $this->renderBS5([
      'main' => fn() => $this->load('tiny')
    ]);
  }

  public function tiny8() {

    $this->data = (object)[
      'title' => $this->title = config::$WEBNAME,
      'pageUrl' => strings::url($this->route),
      'searchFocus' => true,
      'aside' => ['aside']
    ];

    $this->renderBS5([
      'main' => fn() => $this->load('tiny8')
    ]);
  }

  public function serviceWorker() {

    Response::serve(config::serviceWorker());
  }

  public function webWorker() {

    Response::serve(config::webWorker());
  }

  public function Worker($js = '') {

    $this->data = (object)[
      'aside' => config::index_set,
      'pageUrl' => strings::url($this->route),
      'searchFocus' => true,
      'title' => $this->title = config::$WEBNAME,
      'serviceWorker' => strings::url('serviceWorker'),
      'webWorker' => strings::url('webWorker')
    ];

    $this->renderBS5([
      'main' => fn() => $this->load('workers')
    ]);
  }
}
