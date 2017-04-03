<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/
NameSpace dvc;

class timer {
	var $start;

	function __construct() {
		$this->start = $this->getmicrotime();

	}

	protected function getmicrotime(){
		list($usec, $sec) = explode(" ",microtime());
		return ((float)$usec + (float)$sec);

	}

	public function elapsed() {
		$end = $this->getmicrotime();
		$time = round($end - $this->start,2);

		return "$time s";

	}

}
