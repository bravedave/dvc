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

class mailfolder extends bravedave\dvc\dto {
  public $name = '';
  public $fullname = '';
  public $subFolders = false;
  public $type = 0;
  public $delimiter = '.';
}
