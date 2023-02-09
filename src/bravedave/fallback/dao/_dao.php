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

use bravedave;

abstract class _dao extends bravedave\dvc\dao {

  public function __construct(bravedave\dvc\db $db = null) {

    \sys::trace(sprintf('deprecated : please call dvc\dao\_dao directly : %s', get_class($this)), 2);
    parent::__construct($db);
  }
}
