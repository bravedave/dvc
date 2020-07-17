<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc\core;

use dvc\Exceptions\DatapathNotWritable;

abstract class config {
	/**
	 * These come from the Google Developers Portal
	 * If you populate the oauth keys, Google Auth Methods will be enabled
	 * see https://console.cloud.google.com/
	 **/
	static $google_api_key = null;		// for use with google php - server side etc
	static $google_js_api_key = null;		// for use with google maps etc
	static $oauth2_client_id = null; 		// Client ID
	static $oauth2_secret = null;  		// Client Secret
	static $oauth2_redirect = null; 		// Redirect URI
	static $oauth2_scope = 0; 			// Permission to read email
	static $captcha = false; 			// Permission to read email

	const lockdown = false;			// affects the home page and docs page set this and they will require auth

	/**
	 * exposes a popup logon if authentication is required
	 *
	 * if you use this, the home controller must accept the
	 * submission and authenticate
	 **/
	const use_inline_logon = false;
	const allow_password_recovery = false;

	const GMAIL_BASIC = 0;
	const GMAIL_READ = 1;
	const GMAIL_SEND = 2;
	const GMAIL_COMPOSE = 3;
	const GMAIL_COMPOSE_SEND_READ = 4;
	const GMAIL_ALL = 5;

	/**
	 * the Facebook Developers Portal
	 * If you populate these, the Facebook Auth Method will be enabled
	 **/
	static $facebook_oauth2_client_id = null; // Client ID
	static $facebook_oauth2_secret = null;  // Client Secret
	static $facebook_oauth2_redirect = null; // Redirect URI

	/**
	 * Change these to different random strings in the local config
	 * to generate unique encyption for the local system
	 **/
	static $CRYPT_IV = 'hdreWHuR';// 8 bit IV
	static $CRYPT_KEY = 'E4Hhjgs4dfscnuyFFDDE562G';// 24 bit Key

	static $VERSION = '0.0.0';
	static $WEBNAME = 'Example System';
	static $WEBEMAIL = 'webmaster@example.dom';
	static $WEBURL = 'http://example.dom';
	static $FOOTER = 'Brayworth Software Design';

	static $SUPPORT_NAME = 'Software Support';
	static $SUPPORT_EMAIL = 'support@example.dom';
	static $EMAIL_ERRORS_TO_SUPPORT = false;

	static $AUTHENTICATION_EXPIRES_DAYS = 1;

	static $BOOTSTRAP = true;
	static $BOOTSTRAP_REQUIRE_POPPER = false;
	static $BOOTSTRAP_VERSION = '4';

	static $COOKIE_AUTHENTICATION_EXPIRES_DAYS = 7;

	static $CONTENT_SECURITY_ENABLED = true;

	static $DATE_FORMAT = 'Y-m-d';
	static $DATETIME_FORMAT = 'Y-m-d g:ia';
	static $TIME_FORMAT = 'g:ia';

	static $DB_HOST = 'localhost';
	static $DB_TYPE = 'none';	// needs to be mysql or sqlite to run, disable with 'disabled'
	static $DB_NAME = 'dbname';
	static $DB_USER = 'dbuser';
	static $DB_PASS = '';
	static $DB_ALTER_FIELD_STRUCTURES = false;	// experimental

	/*
	*	Caching using APCu, Interfaced through https://www.scrapbook.cash/
	* 	see dao\_dao
	*
	*	NOTE: If you enable this you need to have installed
	*		* APC => dnf install php-pecl-apcu
	*		* matthiasmullie/scrapbook => composer require matthiasmullie/scrapbook
	*/
	static $DB_CACHE = '';	// values = 'APC'
	static $DB_CACHE_PREFIX = '';	// alphanumeric only, optionally create uniqueness for applications
	static $DB_CACHE_TTL = 300;
	static $DB_CACHE_DEBUG = false;
	static $DB_CACHE_DEBUG_FLUSH = false;

	static $DEFAULT_CONTROLLER = 'home';
	static $FONTAWESOME = '4';

	static $EMAILDOMAIN = 'example.com';
	static $EMAIL_STATIONARY = 'email.html';

	static $JQUERY_VERSION = '3.4';

	static $MAILSERVER = 'localhost';
	static $MAILER = 'BrayWorth DVC Mailer 1.0.0 (https://brayworth.com/)';

	static $OFFICE_PHONE = '5555555555';
	static $OFFICE_EMAIL = 'office@domain.tld';

	static $PAGE_TEMPLATE = '\dvc\pages\bootstrap4';
	static $PAGE_TEMPLATE_LOGON = '\dvc\pages\bootstrap4';

	static $PHONE_REGION = 'AU';

	static $PORTAL = 'http://localhost/';
	static $PORTAL_ADMIN = 'http://localhost/';

	static $SITEMAPS = false;
	static $SYNTAX_HIGHLIGHT_DOCS = false;

	static $TEMPLATES_DIR = null;
	static $TEMPLATES_DIR_CSS = null;

	static $THEME = '';

	static $TIMEZONE = 'UTC';

	/*
	settings for the cache expire time
	set in the response headers

	this unit is seconds
	*/
	static $JS_EXPIRE_TIME = 300;
	static $JQUERY_EXPIRE_TIME = 900;
	static $FONT_EXPIRE_TIME = 14400;
	static $CSS_EXPIRE_TIME = 900;
	static $IMG_EXPIRE_TIME = 60;
	static $CORE_IMG_EXPIRE_TIME = 60;	// set on images that come from the /image location

	static public function checkDBconfigured() {
		if ( \config::$DB_TYPE == 'mysql' || \config::$DB_TYPE == 'sqlite' || \config::$DB_TYPE == 'disabled' )
			return true;

		return false;

	}

	static protected $_dataPath = null;

	static public function dataPath() {
		if ( \is_null( self::$_dataPath)) {
			$root = sprintf('%s', \application::app()->getRootPath());
			self::$_dataPath = sprintf('%s%sdata', \application::app()->getRootPath(), DIRECTORY_SEPARATOR );

			if ( is_writable( $root ) || is_writable( self::$_dataPath)) {
				if ( !is_dir( self::$_dataPath)) {
					mkdir( self::$_dataPath, 0777);
					chmod( self::$_dataPath, 0777);

				}

				if ( !file_exists( $readme = self::$_dataPath . DIRECTORY_SEPARATOR . 'readme.txt')) {
					file_put_contents( $readme, implode( PHP_EOL, [
						'-----------',
						'data Folder',
						'-----------',
						'',
						'keep this folder private',
						'',
						'--------------------------------------------',
						'*-* DO NOT UPLOAD TO A PUBLIC REPOSITORY *-*',
						'--------------------------------------------'

					]));

				}

				if ( !is_dir( self::$_dataPath))
					throw new \Exception( 'error/nodatapath');

				return ( self::$_dataPath);

			}

			throw new DatapathNotWritable( self::$_dataPath);

		}

		return self::$_dataPath;

	}

	static protected $_logpath = null;

	static public function logPath() {
		if ( \is_null( self::$_logpath)) {
			self::$_logpath = implode( DIRECTORY_SEPARATOR, [
				rtrim( \config::dataPath(), '/\ '),
				'logs',

			]);

			if ( !is_dir( self::$_logpath)) {
				mkdir( self::$_logpath, 0777);
				chmod( self::$_logpath, 0777);

			}

			// error_log( self::$_logpath );

		}

		return ( self::$_logpath);

	}

	static public function imagePath() {
		return implode( DIRECTORY_SEPARATOR, [ \application::app()->getRootPath(), 'app', 'public', 'images']);

	}

	static public function initialize() {
		/*
		* config initialize is called in _application->__construct()
		*
		* This is a local overwrite of the db and google parameters
		*/
		if ( !( is_null( \application::app()))) {
			// $path = sprintf('%s%sdata', \application::app()->getRootPath(), DIRECTORY_SEPARATOR );
			$path = implode( DIRECTORY_SEPARATOR, [
				\config::dataPath(),
				'db.json'

			]);

			if ( file_exists( $path)) {
				$_a = [
					'db_type' => '',
					'db_host' => '',
					'db_name' => '',
					'db_user' => '',
					'db_pass' => '',
				];
				$a = (object)array_merge( $_a, (array)json_decode( file_get_contents( $path)));
				\config::$DB_TYPE = $a->db_type;
				\config::$DB_HOST = $a->db_host;
				\config::$DB_NAME = $a->db_name;
				\config::$DB_USER = $a->db_user;
				\config::$DB_PASS = $a->db_pass;

			} // if ( file_exists( $path))

			$path = implode( DIRECTORY_SEPARATOR, [
				\config::dataPath(),
				'defaults.json'

			]);

			if ( file_exists( $path)) {
				$_a = [
					'db_type' => \config::$DB_TYPE,
					'date_format' => \config::$DATE_FORMAT,
					'sitemaps' => \config::$SITEMAPS,
					'syntax_highlight_docs' => \config::$SYNTAX_HIGHLIGHT_DOCS,
					'timezone' => \config::$TIMEZONE,
				];

				$a = (object)array_merge( $_a, (array)json_decode( file_get_contents( $path)));

				\config::$DB_TYPE = $a->db_type;
				\config::$DATE_FORMAT = $a->date_format;
				\config::$SITEMAPS = $a->sitemaps;
				\config::$SYNTAX_HIGHLIGHT_DOCS = $a->syntax_highlight_docs;
				\config::$TIMEZONE = $a->timezone;

			} // if ( file_exists( $path))
			else {
				$path = implode( DIRECTORY_SEPARATOR, [
					\config::dataPath(),
					'defaults-sample.json'

				]);

				if ( !file_exists( $path)) {
					$a = [
						'db_type' => 'sqlite',
						'date_format' => 'd/m/Y',
						'sitemaps' => \config::$SITEMAPS,
						'syntax_highlight_docs' => \config::$SYNTAX_HIGHLIGHT_DOCS,
						'timezone' => \config::$TIMEZONE,
					];
					file_put_contents( $path, json_encode( $a, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

				} // if ( file_exists( $path))

			}

			// $path = sprintf('%s%sdata%sgoogle.json',  \application::app()->getRootPath(), DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR );
			$path = sprintf('%s%sgoogle.json', \config::dataPath(), DIRECTORY_SEPARATOR);
			if ( file_exists( $path)) {
				$a = json_decode( file_get_contents( $path));
				if ( isset( $a->web)) {
					\config::$oauth2_client_id = $a->web->client_id;
					\config::$oauth2_secret = $a->web->client_secret;
					\config::$oauth2_redirect = \url::$PROTOCOL . \url::tostring( 'auth/response/');

				} // if ( isset( $a->web))

			} // if ( file_exists( $path))

			// $path = sprintf('%s%sdata%sgoogle.json',  \application::app()->getRootPath(), DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR );
			$path = implode( DIRECTORY_SEPARATOR, [
				\config::dataPath(),
				'recaptcha.json'

			]);

			if ( file_exists( $path)) {
				$a = json_decode( file_get_contents( $path));
				if ( isset( $a->public)) {
					\config::$captcha = (object)[
						'public' => $a->public,
						'private' => $a->private
					];

				} // if ( isset( $a->web))

			} // if ( file_exists( $path))

		} // if ( !( is_null( \application::app())))

	} // static function init2()


	static protected function _route_map_path() : string {
		return self::dataPath() . '/controllerMap.json';

	}

	static protected function _route_map() : object {
		$map = self::_route_map_path();
		if ( \file_exists( $map)) {
			return (object)\json_decode( \file_get_contents( $map));

		}

		return (object)[];

	}

	static public function route_register( string $path, $register = false) {
		$map = self::_route_map();
		if ( !isset( $map->{ $path }) || $register != $map->{ $path }) {
			if ( $register) {
				$map->{ $path } = $register;

			}
			else {
				unset( $map->{ $path });

			}

			\file_put_contents( self::_route_map_path(), \json_encode( $map, JSON_PRETTY_PRINT));

		}

	}

	static public function route( string $path) : string {
		$map = self::_route_map();

		return ( isset( $map->{ $path}) ? $map->{ $path} : '');

	}

	static public function tempdir() {
		/*
		* return a writable path with a trailing slash
		*/

		$dir = rtrim( sys_get_temp_dir(), '/\\');
		return ( $dir . DIRECTORY_SEPARATOR);

	}

}
