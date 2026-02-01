<?php
  // file: src/app/todo/dao/dto/todo.php
  // MIT License

namespace todo\dao\dto;

use bravedave\dvc\dto;

class todo extends dto {
  public $id = 0;
  public $created = '';
  public $updated = '';
  public $description = '';
  public $complete = '';
}