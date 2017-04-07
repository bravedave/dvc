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
	static $MAILER = 'BrayWorth DVC Mailer 1.0.0 (https://brayworth.com/)';

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

	static public function checkDBconfigured() {
		if ( config::$DB_TYPE == 'mysql' )
			return TRUE;

		return FALSE;

	}

	static function dbInit() {
		/** This is a local overwrite of the db parameters
		 */
		if ( !( is_null( \application::app()))) {
			$path = sprintf('%s%sdata', \application::app()->getRootPath(), DIRECTORY_SEPARATOR );
			if ( is_dir( $path)) {
				$path = sprintf('%s%sdb.json', $path, DIRECTORY_SEPARATOR );
				if ( file_exists( $path)) {
					$a = json_decode( file_get_contents( $path));
					if ( isset( $a->db_type)) {
						self::$DB_TYPE = $a->db_type;
						self::$DB_HOST = $a->db_host;
						self::$DB_NAME = $a->db_name;
						self::$DB_USER = $a->db_user;
						self::$DB_PASS = $a->db_pass;

					} // if ( isset( $a['db_type']))

				} // if ( file_exists( $path))

			} // if ( is_dir( $path))

		} // if ( !( is_null( \application::app())))

	} // static function init2()

}

_config::dbInit();
