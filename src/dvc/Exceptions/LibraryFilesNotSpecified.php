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

class LibraryFilesNotSpecified extends Exception {
	protected $_text = 'The files required to create the Library were Not Specified';

}
