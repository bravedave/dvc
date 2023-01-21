<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc\Exceptions;

use bravedave\dvc\Exceptions\Exception;

class InvalidAuthUser extends Exception {
  protected $_text = 'invalid user';
}
