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
	/*
	 * These come from the Google Developers Portal
	 * If you populate the oauth keys, Google Auth Methods will be enabled
	 * see https://console.cloud.google.com/
	 */
	static $google_api_key = NULL;		// for use with google maps etc
	static $oauth2_client_id = NULL; 	// Client ID
	static $oauth2_secret = NULL;  		// Client Secret
	static $oauth2_redirect = NULL; 	// Redirect URI
	static $oauth2_scope = 0; 			// Permission to read email

	const GMAIL_BASIC = 0;
	const GMAIL_READ = 1;
	const GMAIL_SEND = 2;
	const GMAIL_COMPOSE = 3;
	const GMAIL_COMPOSE_SEND_READ = 4;
	const GMAIL_ALL = 5;

	/*
	 * From the Facebook Developers Portal
	 * If you populate these, the Facebook Auth Method will be enabled
	 */
	static $facebook_oauth2_client_id = NULL; // Client ID
	static $facebook_oauth2_secret = NULL;  // Client Secret
	static $facebook_oauth2_redirect = NULL; // Redirect URI

	/*
	 * Change these to different random strings
	 * in the local config to generate unique
	 * encyption for the local system
	 */
	static $CRYPT_IV = 'hdreWHuR';// 8 bit IV
	static $CRYPT_KEY = 'E4Hhjgs4dfscnuyFFDDE562G';// 24 bit Key

	static $VERSION = '0.0.0';
	static $WEBNAME = 'Brayworth Demonstration System';
	static $WEBEMAIL = 'webmaster@example.dom';
	static $FOOTER = 'Brayworth Software Design';

	static $SUPPORT_NAME = 'Software Support';
	static $SUPPORT_EMAIL = 'support@example.dom';
	static $EMAIL_ERRORS_TO_SUPPORT = FALSE;

	static $DB_HOST = 'localhost';
	static $DB_TYPE = 'none';	// needs to be mysql or sqlite to run
	static $DB_NAME = 'dbname';
	static $DB_USER = 'dbuser';
	static $DB_PASS = '';
	static $DB_ALTER_FIELD_STRUCTURES = FALSE;	// experimental

	static $TIMEZONE = 'UTC';
	static $MAILSERVER = 'localhost';
	static $MAILER = 'BrayWorth DVC Mailer 1.0.0 (https://brayworth.com/)';
	static $DATE_FORMAT = 'Y-m-d';
	static $EMAILDOMAIN = 'example.com';
	static $AUTHENTICATION_EXPIRES_DAYS = 1;
	static $COOKIE_AUTHENTICATION_EXPIRES_DAYS = 7;

	static $EMAIL_STATIONARY = 'email.html';
	static $TEMPLATES_DIR = NULL;
	static $TEMPLATES_DIR_CSS = NULL;

	static $CSS_BASE = 'dvc';
	static $BOOTSTRAP = TRUE;

	static $SITEMAPS = FALSE;

	static $DEFAULT_CONTROLLER = 'home';
	static $CREATE_CONTROLLER_SYMLINKS = FALSE;
	static $REMOVE_CONTROLLER_SYMLINKS = FALSE;

	static $CONTENT_SECURITY_ENABLED = TRUE;

	/* settings for the cache expire time
	 * set in the response headers */
	static $JQUERY_EXPIRE_TIME = 900;
	static $FONT_EXPIRE_TIME = 900;
	static $CSS_EXPIRE_TIME = 900;
	static $IMG_EXPIRE_TIME = 60;
	static $CORE_IMG_EXPIRE_TIME = 60;	// set on images that come from the /image location

	static function tempdir() {
		/*
		 * return a writable path with a trailing slash
		 */

		$dir = trim( sys_get_temp_dir(), '/\\');
		return ( $dir . DIRECTORY_SEPARATOR);

	}

	static public function checkDBconfigured() {
		if ( config::$DB_TYPE == 'mysql' )
			return TRUE;

		return FALSE;

	}

	static function dbInit() {
		/** This is a local overwrite of the db and google parameters
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

				$path = sprintf('%s%sdata%sgoogle.json',  \application::app()->getRootPath(), DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR );
				if ( file_exists( $path)) {
					$a = json_decode( file_get_contents( $path));
					if ( isset( $a->web)) {
						self::$oauth2_client_id = $a->web->client_id;
						self::$oauth2_secret = $a->web->client_secret;
						self::$oauth2_redirect = \url::$PROTOCOL . \url::tostring( 'auth/response/');
						//~ print '<pre>';
						//~ printf( '%s<br />', self::$oauth2_client_id);
						//~ printf( '%s<br />', self::$oauth2_secret);
						//~ printf( '%s<br />', self::$oauth2_redirect);
						//~ die;

					} // if ( isset( $a->web))

				} // if ( file_exists( $path))

			} // if ( is_dir( $path))

		} // if ( !( is_null( \application::app())))

	} // static function init2()

}

_config::dbInit();
