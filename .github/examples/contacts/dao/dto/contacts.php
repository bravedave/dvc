<?php
  // file: src/app/contacts/dao/dto/contacts.php
  // MIT License

namespace contacts\dao\dto;

use bravedave\dvc\dto;

class contacts extends dto {
  public $id = 0;
  public $created = '';
  public $updated = '';
  public $name = '';
  public $email = '';
  public $mobile = '';
}