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

	static $WEBNAME = 'Brayworth Demonstration System';

	static $VERSION = '0.0.0';

	static $DB_HOST = 'localhost';
	static $DB_TYPE = 'none';	// needs to be mysql or sqlite to run
	static $DB_NAME = 'dbname';
	static $DB_USER = 'dbuser';
	static $DB_PASS = '';

	static $TIMEZONE = 'UTC';
	static $MAILSERVER = 'localhost';

	static $DEFAULT_CONTROLLER = 'home';
	static $CREATE_CONTROLLER_SYMLINKS = FALSE;
	static $REMOVE_CONTROLLER_SYMLINKS = FALSE;

	static $CONTENT_SECURITY_ENABLED = TRUE;
	static $CSS_BASE = 'dvc';
	static $BOOTSTRAP = TRUE;

	static $SITEMAPS = FALSE;

	/* settings for the cache expire time
	 * set in the response headers */
	static $JQUERY_EXPIRE_TIME = 900;
	static $FONT_EXPIRE_TIME = 900;
	static $CSS_EXPIRE_TIME = 900;
	static $IMG_EXPIRE_TIME = 60;
	static $CORE_IMG_EXPIRE_TIME = 60;	// set on images that come from the /image location

}
