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

use dvc;

abstract class _dao extends dvc\dao\_dao {
  public function __construct(dvc\dbi $db = null) {
    \sys::trace(sprintf('deprecated : please call dvc\dao\_dao directly : %s', get_class($this)), 2);
    parent::__construct($db);
  }
}
