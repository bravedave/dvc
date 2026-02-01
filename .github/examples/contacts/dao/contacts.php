<?php
  // file: src/app/contacts/dao/contacts.php
  // MIT License

namespace contacts\dao;

use bravedave\dvc\{dao, dtoSet};

class contacts extends dao {
  protected $_db_name = 'contacts';
  protected $template = dto\contacts::class;

  public function getMatrix() : array {

    return (new dtoSet)('SELECT * FROM `contacts`'); // an array of records
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