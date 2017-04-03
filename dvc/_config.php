<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	DO NOT change this file
	It is not called directly, the values here are fallback values

	Modify config.php
	*/
NameSpace dvc;

abstract class _config {
	static $EMAIL_ERRORS_TO_SUPPORT = FALSE;

	static $DB_HOST = 'localhost';
	static $DB_TYPE = 'none';	// needs to be mysql or sqlite to run
	static $DB_NAME = 'dbname';
	static $DB_USER = 'dbuser';
	static $DB_PASS = '';

	static $TIMEZONE = 'UTC';
	static $MAILSERVER = 'localhost';

}
