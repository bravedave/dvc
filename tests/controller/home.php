<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

use bravedave\dvc\{controller, json, logger, Response};

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

    $action = $this->getPost('action');

    match ($action) {
      'get-todo-data' => $this->getTodoData($action),
      'todo-add' => $this->todoAdd($action),
      'todo-delete' => $this->todoDelete($action),
      'todo-update' => $this->todoUpdate($action),
      'hello' => json::ack($action),
      default => parent::postHandler()
    };
  }

  protected function getTodoData(string $action): json {

    return json::ack($action)
      ->data((new dao\todo)->getMatrix());
  }

  protected function todoAdd(string $action): json {

    (new dao\todo)->Insert([
      'description' => $this->getPost('description')
    ]);

    return json::ack($action);
  }

  protected function todoDelete(string $action): json {

    if ($id = (int)$this->getPost('id')) {

      (new dao\todo)->delete($id);
      return json::ack($action);
    }

    return json::nak($action);
  }

  protected function todoUpdate(string $action): json {

    if ($id = (int)$this->getPost('id')) {

      (new dao\todo)->UpdateByID([
        'description' => $this->getPost('description')
      ], $id);
      return json::ack($action);
    }

    return json::nak($action);
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

  public function pdo() {

    $pdo = new dvc\pdo\db;

    if ('sqlite' == config::$DB_TYPE) {
      $pdo->exec(
        "CREATE TABLE IF NOT EXISTS `_ux_test` (
          `id` INTEGER PRIMARY KEY AUTOINCREMENT,
          `name` TEXT
        )"
      );
    } else {
      $pdo->exec(
        "CREATE TABLE IF NOT EXISTS `_ux_test` (
          `id` bigint(20) NOT NULL AUTO_INCREMENT,
          `name` varchar(50) DEFAULT '',
          PRIMARY KEY (`id`)
        ) DEFAULT CHARSET=utf8;"
      );
    }

    $add = true;
    if ($res = $pdo->query('SELECT `id`, `name` FROM `_ux_test` WHERE `name` = :name', [':name' => 'David'])) {
      if ($obj = $res->fetchObject('user')) $add = false;
    }

    if ($add) $pdo->insert('_ux_test', ['name' => 'David']);
    $pdo->updateByID('_ux_test', ['name' => 'John'], 1);
    $pdo->updateByID('_ux_test', ['name' => 'Lynne'], 2);
    $pdo->updateByID('_ux_test', ['name' => 'Max'], 3);

    if ($statement = $pdo->query('SELECT `id`, `name` FROM `_ux_test`')) {
      foreach ($statement as $row) {
        printf(
          '%s : %s<br>',
          $row->id,
          $row->name
        );
      }
    }
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
