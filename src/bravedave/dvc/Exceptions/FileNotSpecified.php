<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\dvc\Exceptions;

class FileNotSpecified extends Exception {

	protected $_text = 'the file or path was not specified when calling the function';
}
