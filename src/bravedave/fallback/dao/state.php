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

class state extends dao {
  // protected $_db_name = 'state';	// required if state is a table

  public function offline() {

    return false;
  }
}
