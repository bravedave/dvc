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

use bravedave;

class _dto extends bravedave\dvc\dto {
  public function __construct($row = null) {
    \sys::trace(sprintf('deprecated : please call bravedave\dvc\dto directly : %s', get_class($this)), 2);
    parent::__construct($row);
  }
}
