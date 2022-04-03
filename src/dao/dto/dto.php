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

class dto extends dvc\dao\dto\_dto {
  public function __construct($row = null) {
    \sys::trace(sprintf('deprecated : please call dvc\dao\dto\_dto directly : %s', get_class($this)), 1);
    parent::__construct($row);
  }
}
