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

class NoAuthenticationMethodsAvailable extends Exception {
	protected $_dvc = 'No Authentication Methods Available';

}
