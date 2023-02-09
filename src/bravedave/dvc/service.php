<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\dvc;

class service extends application {
  protected $service = true;

  public function __construct($rootPath) {

    parent::__construct($rootPath);
  }
}
