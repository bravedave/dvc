<?php
  // file: src/app/todo/dao/dbinfo.php
  // MIT License

namespace todo\dao;

use bravedave\dvc\dbinfo as dvcDbInfo;

class dbinfo extends dvcDbInfo {
  protected function check() {
    parent::check();
    parent::checkDIR(__DIR__);
  }
}