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
  protected $template = __NAMESPACE__ . '\dto\todo';

  function getMatrix(): array {

    if ($res = $this->getAll()) return $res->dtoSet();
    return [];
  }
}
