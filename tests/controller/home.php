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

    // sys::telegram()
    //   ->info(config::$PAGE_TEMPLATE, [__METHOD__]);
    // echo $barney;  // which is an error

    // sys::monolog($email = true)
    //   ->error(config::$PAGE_TEMPLATE);

    if ('4' == config::$BOOTSTRAP_VERSION) {
      $this->render([
        'secondary' => ['aside'],
        'navbar' => 'navbar-4',
        'primary' => ['main']
      ]);
    } else {
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
      \sys::logger(sprintf('<%s> %s', config::$PAGE_TEMPLATE, __METHOD__));
      $this->render([
        'secondary' => ['aside'],
        'primary' => ['main']
      ]);
    }
  }
}
