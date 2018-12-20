<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/
namespace dao\dto;

class mailfolder extends _dto {
	public $name = '';
	public $fullname = '';
	public $subFolders = false;
	public $type = 0;
	public $delimiter = '.';

}
