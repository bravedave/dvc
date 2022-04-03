<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dao\dto;

use dvc;

class _dto extends dvc\dao\dto\_dto {
  public function __construct($row = null) {
    \sys::logger(sprintf('deprecated : please call dvc\dao\dto\_dto directly : %s', get_class($this)));
    parent::__construct($row);
  }
}
