<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

use bravedave\dvc\logger;

class home extends Controller {

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

      $this->renderBS5([
        'main' => fn () => $this->load('main')
      ]);
    }
  }

  protected function before() {

    $this->viewPath[] = __DIR__ . '/views/';
    parent::before();
  }

  protected function postHandler() {

    $action = $this->getPost('action');
    parent::postHandler();
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

  public function tiny() {

    if ('4' == config::$BOOTSTRAP_VERSION) {
      $this->render([
        'secondary' => ['aside'],
        'navbar' => 'navbar-4',
        'primary' => ['tiny']
      ]);
    } else {
      logger::info(sprintf('<%s> %s', config::$PAGE_TEMPLATE, __METHOD__));
      $this->render([
        'secondary' => ['aside'],
        'primary' => ['main']
      ]);
    }
  }
}
