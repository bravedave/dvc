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

use dvc\dao\dto\_dto;

class mailfolder extends _dto {
	public $name = '';
	public $fullname = '';
	public $subFolders = false;
	public $type = 0;
	public $delimiter = '.';

}
