<?php
  // file: src/app/todo/config.php
  // MIT License

namespace todo;

use config as rootConfig;

class config extends rootConfig {

  const todo_db_version = 1;

  const label = 'Todo';

  static function todo_checkdatabase() {

    $dao = new dao\dbinfo;
    $dao->checkVersion('todo', self::todo_db_version);
  }
}