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

class InvalidPostAction extends Exception {
	protected $_text = 'This post action is not allowed';

}
