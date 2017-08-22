<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/
Namespace dvc\sqlite;

abstract class _dbinfo {
	protected $db;

	function __construct( db $db = NULL) {
		$this->db = $db;

	}

	function check() {}

}

