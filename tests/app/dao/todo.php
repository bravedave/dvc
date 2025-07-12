<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dao;

use bravedave\dvc\dao;

class todo extends dao {
  protected $_db_name = 'todo';
  protected $template = dto\todo::class;

  function getMatrix(): array {

    $query = 'SELECT * FROM ' . $this->_db_name . ' ORDER BY id DESC';
    $stmt = $this->statement($query);
    // $stmt->bind([]); // no parameters to bind, so not required
    return $stmt->dtoSet(null, $this->template);
  }
}
