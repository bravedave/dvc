<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * TODO: \config:: should be replace with static::
 *  => https://www.php.net/manual/en/language.oop5.late-static-bindings.php
*/

namespace bravedave\dvc;

use bravedave\dvc\Exceptions\{DatapathNotFound, DatapathNotWritable};

abstract class config {

  const index_set = ['blank'];
  const navbar_default = 'navbar-default';

  /**
   * These come from the Google Developers Portal
   * If you populate the oauth keys, Google Auth Methods will be enabled
   * see https://console.cloud.google.com/
   **/
  static $google_api_key = null;    // for use with google php - server side etc
  static $google_js_api_key = null;    // for use with google maps etc
  static $oauth2_client_id = null;     // Client ID
  static $oauth2_secret = null;      // Client Secret
  static $oauth2_redirect = null;     // Redirect URI
  static $oauth2_scope = 0;       // Permission to read email
  static $captcha = false;       // Permission to read email

  const lockdown = false;      // affects the home page and docs page set this and they will require auth

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
  static $CIPHER = 'aes-128-cbc';
  static $CRYPT_IV = 'hdreWHuRjkdftyhc'; // 8 bit IV
  static $CRYPT_KEY = 'E4Hhjgs4dfscnuyFFDDE562G'; // 24 bit Key

  static $VERSION = '0.0.0';
  static $WEBNAME = 'Example System';
  static $WEBEMAIL = 'webmaster@example.dom';
  static $WEBURL = 'http://example.dom';
  static $FOOTER = 'Brayworth Software Design';

  static $SUPPORT_NAME = 'IT Support';
  static $SUPPORT_EMAIL = 'itsupport@example.dom';
  static $EMAIL_ERRORS_TO_SUPPORT = false;

  static $AUTHENTICATION_EXPIRES_DAYS = 1;

  static $BOOTSTRAP = true;
  static $BOOTSTRAP_REQUIRE_POPPER = false;
  static $BOOTSTRAP_VERSION = '4';

  static $COOKIE_AUTHENTICATION_EXPIRES_DAYS = 7;

  static $CONTENT_SECURITY_ENABLED = true;

  static $DATE_FORMAT = 'Y-m-d';
  static $DATE_FORMAT_LONG = 'D M d Y';
  static $DATETIME_FORMAT = 'Y-m-d g:ia';
  static $DATETIME_FORMAT_LONG = 'D M d Y g:ia';
  static $FONTAWESOME = false;
  static $TIME_FORMAT = 'g:ia';

  static $FREE_DISKSPACE_THRESHHOLD = 10485760000; // 10G

  static $DB_HOST = 'localhost';
  static $DB_TYPE = 'none';  // needs to be mysql or sqlite to run, disable with 'disabled'
  static $DB_NAME = 'dbname';
  static $DB_USER = 'dbuser';
  static $DB_PASS = '';
  static $DB_ALTER_FIELD_STRUCTURES = false;  // experimental

  /*
	*	Caching using APCu, Interfaced through https://www.scrapbook.cash/
	* 	see dao\_dao
	*
	*	NOTE: If you enable this you need to have installed
	*		* APC => dnf install php-pecl-apcu
	*		* matthiasmullie/scrapbook => composer require matthiasmullie/scrapbook
	*/
  static $DB_CACHE = '';  // values = 'APC'
  static $DB_CACHE_PREFIX = '';  // alphanumeric only, optionally create uniqueness for applications
  static $DB_CACHE_TTL = 600; // 10 minutes
  static $DB_CACHE_DEBUG = false;
  static $DB_CACHE_DEBUG_FLUSH = false;
  static $DB_CACHE_DEBUG_TYPE_CONFLICT = true;

  static $DEFAULT_CONTROLLER = 'home';

  static $EMAILDOMAIN = 'example.tld';
  static $EMAIL_STATIONARY = 'email.html';

  static $HTML_TICK = '&#10003;';

  static $IMAP_AUTH_SERVER = '';

  // static $JQUERY_VERSION = '3.4';
  static $JQUERY_VERSION = '';

  static $LOG_DEBUG = true;
  static $LOG_DEPRECATED = false;

  static $MAILDSN = '';
  static $MAILSERVER = 'localhost';
  static $MAILER = 'BrayWorth DVC Mailer 1.0.1 (https://brayworth.com/)';

  static $OFFICE_PHONE = '5555555555';
  static $OFFICE_EMAIL = 'office@domain.tld';

  static $PAGE_LAYOUT = '';
  static $PAGE_TEMPLATE = '\dvc\pages\bootstrap4';
  static $PAGE_TEMPLATE_APP = '\dvc\pages\bootstrap4_app';
  static $PAGE_TEMPLATE_LOGON = '\dvc\pages\bootstrap4';

  static $PHONE_REGION = 'AU';

  static $PORTAL = 'http://localhost/';
  static $PORTAL_ADMIN = 'http://localhost/';

  static $SAMESITE_POLICY = 'lax';  // none, lax, strict
  static $SESSION_CACHE_EXPIRE = 180;
  static $SITEMAPS = false;
  static $SYNTAX_HIGHLIGHT_DOCS = false;

  static $TELEGRAM_API_KEY = '';
  static $TELEGRAM_CHAT_ID = '';

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
  static $CORE_IMG_EXPIRE_TIME = 60;  // set on images that come from the /image location

  static $UMASK = '0022';

  static public function checkDBconfigured() {
    if (\config::$DB_TYPE == 'mysql' || \config::$DB_TYPE == 'sqlite' || \config::$DB_TYPE == 'disabled')
      return true;

    return false;
  }

  static protected ?string $_dataPath = null;

  /**
   * the default location for storing data
   *
   * @return string
   */
  static public function dataPath(): string {

    if (\is_null(static::$_dataPath)) {

      $root = sprintf('%s', \application::app()->getRootPath());

      $datapath = sprintf('%s%sdata', \application::app()->getRootPath(), DIRECTORY_SEPARATOR);

      /**
       * the location of the datapath can be changed by
       * including a 'datapath' file in the default
       * datapath location
       */
      if (is_dir($datapath) && file_exists($_redir_file = $datapath . '/datapath')) {
        $_redir = file_get_contents($_redir_file);
        if (is_dir($_redir) && is_writable($_redir)) {
          $datapath = $_redir;
        }
      }

      static::$_dataPath = $datapath;
      if (is_writable($root) || is_writable(static::$_dataPath)) {

        if (!is_dir(static::$_dataPath)) mkdir(static::$_dataPath);
        if (!file_exists($readme = static::$_dataPath . DIRECTORY_SEPARATOR . 'readme.txt')) {

          file_put_contents($readme, implode(PHP_EOL, [
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

        if (!file_exists($ignore = static::$_dataPath . DIRECTORY_SEPARATOR . '.gitignore')) file_put_contents($ignore, '*');
        if (!is_dir(static::$_dataPath)) throw new DatapathNotFound(static::$_dataPath);

        return static::$_dataPath;
      }

      throw new DatapathNotWritable(static::$_dataPath);
    }

    return static::$_dataPath;
  }

  static protected $_options_ = [];

  static protected function _options_file() {
    return implode(DIRECTORY_SEPARATOR, [
      static::dataPath(),
      '_config_.json'

    ]);
  }

  public static function getDBCachePrefix(): string {

    if (config::$DB_CACHE_PREFIX) return config::$DB_CACHE_PREFIX;

    $path = implode(DIRECTORY_SEPARATOR, [
      static::dataPath(),
      'dbCachePrefix.json'
    ]);

    if (file_exists($path)) {

      $j = json_decode(file_get_contents($path));
      static::$DB_CACHE_PREFIX = $j->prefix;
      return config::$DB_CACHE_PREFIX;
    } else {

      $a = (object)['prefix' => bin2hex(random_bytes(6))];
      file_put_contents($path, \json_encode($a));
      static::$DB_CACHE_PREFIX = $a->prefix;
      return static::$DB_CACHE_PREFIX;
    }
  }

  /**
   * global available settings
   *
   * @return string
   */
  public static function option(string $key, string $val = null): string {
    $ret = '';

    if (!static::$_options_) {
      if (file_exists($path = static::_options_file())) {
        static::$_options_ = (array)json_decode(file_get_contents($path));
      }
    }

    if (isset(static::$_options_[$key])) $ret = (string)static::$_options_[$key];

    if (!is_null($val)) {

      /* writer */
      if ((string)$val == '') {
        if (isset(static::$_options_[$key])) {
          unset(static::$_options_[$key]);
        }
      } else {
        static::$_options_[$key] = (string)$val;
      }

      file_put_contents(
        static::_options_file(),
        json_encode(
          static::$_options_,
          JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT

        )

      );
    }

    return $ret;
  }

  public static function defaultsPath() {
    return implode(DIRECTORY_SEPARATOR, [
      \config::dataPath(),
      'defaults.json'

    ]);
  }

  static protected $_logpath = null;

  public static function logPath() {
    if (\is_null(static::$_logpath)) {
      static::$_logpath = implode(DIRECTORY_SEPARATOR, [
        rtrim(\config::dataPath(), '/\ '),
        'logs',

      ]);

      if (!is_dir(static::$_logpath)) {
        mkdir(static::$_logpath);
      }

      // error_log( static::$_logpath );

    }

    return (static::$_logpath);
  }

  public static function imagePath() {
    return implode(DIRECTORY_SEPARATOR, [\application::app()->getRootPath(), 'app', 'public', 'images']);
  }

  public static function initialize() {
    /**
     * config initialize is called in _application->__construct()
     *
     * This is a local overwrite of the db and google parameters
     */

    // logger::info( sprintf('%s', __METHOD__));

    if (!(is_null(\application::app()))) {

      umask(octdec(static::$UMASK));  // in case everything needs creating

      // $path = sprintf('%s%sdata', \application::app()->getRootPath(), DIRECTORY_SEPARATOR );
      $path = implode(DIRECTORY_SEPARATOR, [
        \config::dataPath(),
        'db.json'

      ]);

      if (file_exists($path)) {
        $_a = [
          'db_type' => '',
          'db_host' => '',
          'db_name' => '',
          'db_user' => '',
          'db_pass' => '',
        ];
        $a = (object)array_merge($_a, (array)json_decode(file_get_contents($path)));
        \config::$DB_TYPE = $a->db_type;
        \config::$DB_HOST = $a->db_host;
        \config::$DB_NAME = $a->db_name;
        \config::$DB_USER = $a->db_user;
        \config::$DB_PASS = $a->db_pass;
      } // if ( file_exists( $path))

      $path = static::defaultsPath();
      if (file_exists($path)) {
        $_a = [
          'db_type' => \config::$DB_TYPE,
          'db_cache' => \config::$DB_CACHE,
          'db_cache_debug' => \config::$DB_CACHE_DEBUG,
          'db_cache_debug_flush' => \config::$DB_CACHE_DEBUG_FLUSH,
          'db_cache_debug_type_conflict' => \config::$DB_CACHE_DEBUG_TYPE_CONFLICT,
          'date_format' => \config::$DATE_FORMAT,
          'datetime_format' => \config::$DATETIME_FORMAT,
          'email_errors_to_support' => \config::$EMAIL_ERRORS_TO_SUPPORT,
          'emaildomain' => \config::$EMAILDOMAIN,
          'imap_auth_server' => \config::$IMAP_AUTH_SERVER,
          'maildsn' => \config::$MAILDSN,
          'page_template' => \config::$PAGE_TEMPLATE,
          'samesite_policy' => \config::$SAMESITE_POLICY,
          'session_cache_expire' => \config::$SESSION_CACHE_EXPIRE,
          'sitemaps' => \config::$SITEMAPS,
          'syntax_highlight_docs' => \config::$SYNTAX_HIGHLIGHT_DOCS,
          'support_name' => \config::$SUPPORT_NAME,
          'support_email' => \config::$SUPPORT_EMAIL,
          'telegram_api_key' => \config::$TELEGRAM_API_KEY,
          'telegram_chat_id' => \config::$TELEGRAM_CHAT_ID,
          'timezone' => \config::$TIMEZONE,
          'theme' => \config::$THEME,
          'umask' => \config::$UMASK,
        ];

        $a = (object)array_merge($_a, (array)json_decode(file_get_contents($path)));

        \config::$DB_TYPE = $a->db_type;
        \config::$DB_CACHE = $a->db_cache;
        \config::$DB_CACHE_DEBUG = $a->db_cache_debug;
        \config::$DB_CACHE_DEBUG_FLUSH = $a->db_cache_debug_flush;
        \config::$DB_CACHE_DEBUG_TYPE_CONFLICT = $a->db_cache_debug_type_conflict;
        \config::$DATE_FORMAT = $a->date_format;
        \config::$DATETIME_FORMAT = $a->datetime_format;

        \config::$EMAIL_ERRORS_TO_SUPPORT = $a->email_errors_to_support;
        \config::$EMAILDOMAIN = $a->emaildomain;

        \config::$IMAP_AUTH_SERVER = $a->imap_auth_server;
        \config::$MAILDSN = $a->maildsn;

        \config::$PAGE_TEMPLATE = $a->page_template;

        \config::$SAMESITE_POLICY = $a->samesite_policy;
        \config::$SESSION_CACHE_EXPIRE = $a->session_cache_expire;
        \config::$SITEMAPS = $a->sitemaps;
        \config::$SYNTAX_HIGHLIGHT_DOCS = $a->syntax_highlight_docs;
        \config::$SUPPORT_NAME = $a->support_name;
        \config::$SUPPORT_EMAIL = $a->support_email;

        \config::$TELEGRAM_API_KEY = $a->telegram_api_key;
        \config::$TELEGRAM_CHAT_ID = $a->telegram_chat_id;
        \config::$TIMEZONE = $a->timezone;
        \config::$THEME = $a->theme;

        \config::$UMASK = $a->umask;
      } // if ( file_exists( $path))
      else {

        $path = implode(DIRECTORY_SEPARATOR, [
          \config::dataPath(),
          'defaults-sample.json'
        ]);

        if (!file_exists($path)) {
          $a = [
            'db_type' => 'sqlite',
            'db_cache' => \config::$DB_CACHE,
            'db_cache_debug' => \config::$DB_CACHE_DEBUG,
            'db_cache_debug_flush' => \config::$DB_CACHE_DEBUG_FLUSH,
            'db_cache_debug_type_conflict' => \config::$DB_CACHE_DEBUG_TYPE_CONFLICT,
            'date_format' => 'd/m/Y',
            'datetime_format' => 'd/m/Y g:ia',
            'email_errors_to_support' => \config::$EMAIL_ERRORS_TO_SUPPORT,
            'emaildomain' => \config::$EMAILDOMAIN,
            'imap_auth_server' => \config::$IMAP_AUTH_SERVER,
            'maildsn' => 'smtp://mail:25?verify_peer=0',
            'page_template' => \config::$PAGE_TEMPLATE,
            'samesite_policy' => \config::$SAMESITE_POLICY,
            'session_cache_expire' => \config::$SESSION_CACHE_EXPIRE,
            'sitemaps' => \config::$SITEMAPS,
            'syntax_highlight_docs' => \config::$SYNTAX_HIGHLIGHT_DOCS,
            'telegram_api_key' => \config::$TELEGRAM_API_KEY,
            'telegram_chat_id' => \config::$TELEGRAM_CHAT_ID,
            'timezone' => \config::$TIMEZONE,
            'theme' => \config::$THEME,
            'support_name' => \config::$SUPPORT_NAME,
            'support_email' => \config::$SUPPORT_EMAIL,
            'umask' => \config::$UMASK,
          ];

          file_put_contents($path, json_encode($a, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        } // if ( file_exists( $path))

      }

      if (\config::$DB_CACHE == 'APC') {

        $apcuAvailabe = function_exists('apcu_enabled') && apcu_enabled();
        if (!$apcuAvailabe) {
          logger::info(sprintf('<WARNING : APCu enabled but not available - disabling> %s', __METHOD__));
          \config::$DB_CACHE = '';
        }
      }

      if ('\dvc\pages\bootstrap5' == \config::$PAGE_TEMPLATE) bs::$VERSION = '5';

      // $path = sprintf('%s%sdata%sgoogle.json',  \application::app()->getRootPath(), DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR );
      $path = sprintf('%s%sgoogle.json', \config::dataPath(), DIRECTORY_SEPARATOR);
      if (file_exists($path)) {
        $a = json_decode(file_get_contents($path));
        if (isset($a->web)) {
          \config::$oauth2_client_id = $a->web->client_id;
          \config::$oauth2_secret = $a->web->client_secret;
          \config::$oauth2_redirect = \url::$PROTOCOL . \url::tostring('auth/response/');
        } // if ( isset( $a->web))

      } // if ( file_exists( $path))

      // $path = sprintf('%s%sdata%sgoogle.json',  \application::app()->getRootPath(), DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR );
      $path = implode(DIRECTORY_SEPARATOR, [
        \config::dataPath(),
        'recaptcha.json'

      ]);

      if (file_exists($path)) {
        $a = json_decode(file_get_contents($path));
        if (isset($a->public)) {
          \config::$captcha = (object)[
            'public' => $a->public,
            'private' => $a->private
          ];
        } // if ( isset( $a->web))

      } // if ( file_exists( $path))

    } // if ( !( is_null( \application::app())))

    umask(octdec(static::$UMASK));
  } // static function init2()

  public static function notification_KeyPath() {
    $path = implode(DIRECTORY_SEPARATOR, [
      static::dataPath(),
      'notificationKeys'

    ]);

    if (!is_dir($path)) {
      mkdir($path);
    }

    return $path;
  }

  public static function notification_keys(): object {

    $a = [
      'pubKey' => '',
      'privKey' => ''
    ];

    $pubPath = implode(
      DIRECTORY_SEPARATOR,
      [
        static::notification_KeyPath(),
        'public_key.txt'
      ]
    );

    $privPath = implode(
      DIRECTORY_SEPARATOR,
      [
        static::notification_KeyPath(),
        'private_key.txt'
      ]
    );

    if (file_exists($privPath) && file_exists($pubPath)) {

      $a['privKey'] = file_get_contents($privPath);
      $a['pubKey'] = file_get_contents($pubPath);
    } else {

      /**
       * they need to be created
       */

      if (\class_exists('Minishlink\WebPush\VAPID')) {

        $keys = (object)\Minishlink\WebPush\VAPID::createVapidKeys();
        $a['privKey'] = $keys->privateKey;
        $a['pubKey'] = $keys->publicKey;

        if (file_exists($privPath)) @unlink($privPath);
        if (file_exists($pubPath)) @unlink($pubPath);

        file_put_contents($privPath, $keys->privateKey);
        file_put_contents($pubPath, $keys->publicKey);
      }
    }

    return (object)$a;
  }

  protected static function _route_map_path(): string {

    return static::dataPath() . '/controllerMap.json';
  }

  protected static function _route_map(): object {

    $defaults = array_filter(
      [
        'assets' => 'bravedave\dvc\controller\assets',
        'auth' => 'bravedave\dvc\controller\auth',
        'docs' => 'bravedave\dvc\controller\docs',
        'fbauth' => 'bravedave\dvc\controller\fbauth',
        'install' => 'bravedave\dvc\controller\install',
        'logon' => 'bravedave\dvc\controller\logon',
        'sitemap' => 'bravedave\dvc\controller\sitemap',
      ],
      fn ($k) => file_exists(sprintf('%s/controller/%s.php', __DIR__, $k)),
      ARRAY_FILTER_USE_KEY
    );

    $map = static::_route_map_path();
    if (file_exists($map)) {

      return (object)array_merge(
        $defaults,
        (array)json_decode(file_get_contents($map))
      );
    }

    return (object)$defaults;
  }

  /**
   * set routes for controller
   * leave the second parameter blank to clear the setting
   *
   * @return void
   */
  public static function route_register(string $path, $register = false): void {

    $map = static::_route_map();
    if (!isset($map->{$path}) || $register != $map->{$path}) {

      if ($register) {

        $map->{$path} = $register;
      } else {

        unset($map->{$path});
      }

      file_put_contents(static::_route_map_path(), json_encode($map, JSON_PRETTY_PRINT));
    }
  }

  public static function route(string $path): string {
    $map = static::_route_map();

    return (isset($map->{$path}) ? $map->{$path} : '');
  }

  public static function tempdir() {
    /*
		* return a writable path with a trailing slash
		*/

    $dir = rtrim(sys_get_temp_dir(), '/\\');
    return ($dir . DIRECTORY_SEPARATOR);
  }
}
