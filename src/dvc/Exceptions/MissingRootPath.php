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

class MissingRootPath extends Exception {
	protected $_text = 'you must provide a path';

}
