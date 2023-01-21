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

class ViewNotFound extends Exception {
	protected $_text = 'the view was not found';

}
