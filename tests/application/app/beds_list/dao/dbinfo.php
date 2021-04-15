<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace beds_list\dao;

use dao;

class dbinfo extends dao\_dbinfo {
	/*
	 * it is probably sufficient to copy this file into the
	 * 	<application>/app/dao folder
	 *
	 * from there store you structure files in
	 * 	<application>/dao/db folder
	 */
	protected function check() {
		parent::check();
		parent::checkDIR( __DIR__);

	}

}
