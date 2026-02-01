<?php
  // file: src/app/todo/dao/todo.php
  // MIT License

namespace todo\dao;

use bravedave\dvc\{dao, dtoSet};

class todo extends dao {
  protected $_db_name = 'todo';
  protected $template = dto\todo::class;

  public function getMatrix() : array {

    return (new dtoSet)('SELECT * FROM `todo`'); // an array of records
  }

  public function Insert($a) {
    $a['created'] = $a['updated'] = self::dbTimeStamp();
    return parent::Insert($a);
  }

  public function UpdateByID($a, $id) {
    $a['updated'] = self::dbTimeStamp();
    return parent::UpdateByID($a, $id);
  }
}