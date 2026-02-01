<?php
  // file: src/app/contacts/dao/dbinfo.php
  // MIT License

namespace contacts\dao;

use bravedave\dvc\dbinfo as dvcDbInfo;

class dbinfo extends dvcDbInfo {
  protected function check() {
    parent::check();
    parent::checkDIR(__DIR__);
  }
}