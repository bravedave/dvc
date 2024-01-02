<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\dvc\html;

class dump extends div {

	function __construct($content = null, $title = null) {
		parent::__construct();

		if ((string)$title != '')
			$this->append('h1', $title);

		$this->append('pre', print_r($content, true));
	}
}
