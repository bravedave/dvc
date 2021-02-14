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

use session;

class users extends _dao {
  public function validate() {
    session::edit();
    session::set('valid', 1);
    session::close();

  }

}
