<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc;

use bravedave;

class service extends bravedave\dvc\application {
  protected $service = true;

  public function __construct($rootPath) {
    parent::__construct($rootPath);
  }
}
