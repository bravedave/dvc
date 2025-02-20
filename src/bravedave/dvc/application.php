<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\dvc;

use config, strings;

use bravedave\dvc\Exceptions\{
  SecurityException,
  CannotLocateController,
  MissingRootPath
};

define('APPLICATION', 1);

class application {
  protected static ?Request $_request = null;
  protected static $instance = null;
  protected $_app_executed = false;
  public $exclude_from_sitemap = false;
  protected $rootPath = null;
  protected $_route = null;
  protected $banned = [];
  protected $paths = [];
  protected $url_action = null;
  protected $url_controller = null;
  protected $url_parameter_1 = null;
  protected $url_parameter_2 = null;
  protected $url_parameter_3 = null;
  protected $url_served = '';
  protected timer $_timer;
  protected $db = false;
  public $defaultController = null;
  protected $minimum = false;
  protected $service = false;
  const use_full_url = true;
  static $debug = false;

  public function __construct($rootPath) {
    self::$instance = $this;
    $this->rootPath = realpath($rootPath);
    $this->initialize();
  }

  public static function app() {
    return self::$instance;
  }

  public static function isService() {
    return self::$instance ? self::$instance->service : false;

    //   if (self::$instance) return self::$instance->service;
    //   return (false);
  }

  public static function Request() {

    if (is_null(self::$_request)) {
      self::$_request = Request::get();
    }
    return self::$_request;
  }

  public static function route() {
    return self::$instance->_route;
  }

  public static function timer(): timer {
    return self::$instance->_timer ?? new timer;

    // if (self::$instance) {

    //   if (self::$instance->_timer) return self::$instance->_timer;
    // }

    // return new timer;
  }

  public static function elapsed(): string {
    return self::timer()->elapsed();
  }

  private function initialize() {

    errsys::initiate(false);
    $this->_timer = new timer;
    config::initialize();  // this initializes config
    $this->setConfiguration();

    if ($this->_isPublicFile()) return;

    controller::application($this);

    $this->checkDB();

    if ($this->minimum) {

      if (self::$debug) logger::debug(sprintf('<exit: I am minimum> %s', __METHOD__));
      return;  // job done
    }

    $this->countVisit();
    if ($ip = self::Request()->getRemoteIP()) {

      if (in_array($ip, $this->banned)) {

        header("HTTP/1.1 400 Bad Request");
        print "Bad Request";
        logger::info(sprintf('<banned %s> %s', $ip, logger::caller()));
        die;
      }
    }
    $this->processRequest();
  }

  private function processRequest() {

    $this->_splitUrl();    // create array with URL parts in $url

    /*
		 * example: if controller would be "car",
		 * then this line would translate into: $this->car = new car();
		 */
    if (is_null($this->defaultController)) $this->defaultController = config::$DEFAULT_CONTROLLER;
    if (trim($this->url_controller == '')) $this->url_controller = $this->defaultController;

    if ($this->service) {

      if (self::$debug) logger::debug(sprintf('<exit: I am a service> %s', __METHOD__));
      return;  // job done
    }

    if ($_route = config::route($this->url_controller)) {

      $this->_route = $this->url_controller;
      $this->url_controller = $_route;
    } else {

      $this->_search_for_controller();
      $this->_route = $this->url_controller;
    }

    /*
		* Quiet Security - some actions are protected
		* from outside calling, don't broadcast the error
		*/
    $_protectedActions = [
      '__construct',
      '__destruct',
      'application',
      'authorize',
      'before',
      'dbResult',
      'dbEscape',
      'getParam',
      'hasView',
      'getView',
      'loadView',
      'load',
      'init',
      'page',
      'render',
      'isPost',
      'getPost',
      'sql'
    ];

    if ($this->url_action) {
      if (in_array(strtolower($this->url_action), $_protectedActions)) {

        logger::info(sprintf('<protecting action %s => %s> %s', $this->url_action, 'index', __METHOD__));
        $this->url_action = 'index';
      }
    } else {

      $this->url_action = 'index';
    }

    self::Request()->setControllerName($this->url_controller);
    self::Request()->setActionName((string)$this->url_action);

    $url_controller_name = $this->url_controller;

    $this->url_controller = new $this->url_controller($this->rootPath);
    $this->url_controller->name = $url_controller_name;
    $this->url_controller->timer = $this->_timer;

    $this->url_controller->init($url_controller_name);

    /**
     * Between here and the end of this function the application will execute
     *
     * check for method: does such a method exist in the controller ?
     */
    if (method_exists($this->url_controller, $this->url_action)) {

      // check if the method is public
      $reflection = new \ReflectionMethod($this->url_controller, $this->url_action);
      if ($reflection->isPublic()) {

        $this->url_served = strings::url(implode('/', [
          self::Request()->getControllerName(),
          self::Request()->getActionName()
        ]), $protcol = true);

        // call the method and pass the arguments to it
        if (isset($this->url_parameter_3)) {

          // will translate to something like $this->home->method($param_1, $param_2, $param_3);
          if (self::$debug) logger::debug(sprintf(
            '%s->{%s}(%s, %s, %s)',
            $this->url_controller->name,
            $this->url_action,
            $this->url_parameter_1,
            $this->url_parameter_2,
            $this->url_parameter_3
          ));

          $this->url_controller->{$this->url_action}(
            $this->url_parameter_1,
            $this->url_parameter_2,
            $this->url_parameter_3
          );
        } elseif (isset($this->url_parameter_2)) {

          if (self::$debug) logger::debug(sprintf(
            '%s->{%s}(%s, %s)',
            $this->url_controller->name,
            $this->url_action,
            $this->url_parameter_1,
            $this->url_parameter_2
          ));

          // will translate to something like $this->home->method($param_1, $param_2);
          $this->url_controller->{$this->url_action}(
            $this->url_parameter_1,
            $this->url_parameter_2
          );
        } elseif (isset($this->url_parameter_1)) {

          if (self::$debug) logger::debug(sprintf(
            '%s->{%s}(%s)',
            $this->url_controller->name,
            $this->url_action,
            $this->url_parameter_1
          ));

          // will translate to something like $this->home->method($param_1);
          $this->url_controller->{$this->url_action}($this->url_parameter_1);
        } else {

          if (self::$debug) logger::debug(sprintf(
            '%s->{%s}()',
            $this->url_controller->name,
            $this->url_action
          ));

          /**
           * if no parameters given, just call the method without parameters,
           * like $this->home->method();
           */
          $this->url_controller->{$this->url_action}();
        }
      } else {

        $this->url_served = strings::url(
          self::Request()->getControllerName(),
          $protcol = true
        );

        if (self::$debug) logger::debug('fallback on protected method');
        if (self::$debug) logger::debug(sprintf('%s->index(%s)', $this->url_controller->name, $this->url_action));

        $this->url_controller->index($this->url_action);
      }
    } else {

      $this->url_served = strings::url(
        self::Request()->getControllerName(),
        $protcol = true
      );

      if (self::$debug) logger::debug('fallback');
      if (self::$debug) logger::debug(sprintf('%s->index(%s)', $this->url_controller->name, $this->url_action));

      $this->url_controller->index($this->url_action);
    }

    $this->_app_executed = true;
  }

  private function setConfiguration() {
    if (self::$debug) logger::debug(sprintf('<rootpath :: %s> %s', $this->rootPath, __METHOD__));
    ini_set('date.timezone', config::$TIMEZONE);
    ini_set('SMTP', config::$MAILSERVER);
  }

  public function __destruct() {

    $debug = false;
    // $debug = true;

    if ($this->app_executed()) {

      if (config::$SITEMAPS) {

        $path = $this->return_url();

        try {

          $dao = new \dao\sitemap;
          if ($dto = $dao->getbyPath($path)) {

            if ($debug) logger::debug(sprintf('found path : %s : %s', $path, __METHOD__));
            $dao->UpdateByID(
              ['visits' => $dto->visits + 1],
              (int)$dto->id
            );
          } else {

            if ($debug) logger::debug(sprintf('not found path : %s : %s', $path, __METHOD__));
            $a = [
              'path' => $path,
              'visits' => 1,
              'exclude_from_sitemap' => ((bool)$this->exclude_from_sitemap ? 1 : 0)
            ];

            $dao->Insert($a);
          }
        } catch (\Exception $e) {

          error_log($e->getMessage());
        }
      } else {

        if ($debug) logger::debug(sprintf('<%s> %s', 'not enabled', __METHOD__));
      }
    }

    if (method_exists($this, 'deprecated')) $this->{'deprecated'}();
  }

  protected function _search_for_controller(): void {

    $controllerFile = sprintf('%s/controller/%s.php', $this->rootPath, $this->url_controller);
    /*---[ check for controller: does such a controller exist ? ]--- */

    if (!file_exists($controllerFile)) {

      $controllerFile = sprintf('%s/controller/%s.php', $this->rootPath, $this->defaultController);      // invalid URL, so show home/index
      if (!file_exists($controllerFile)) {

        $controllerFile = sprintf('%s/controller/%s.php', $this->rootPath, config::$DEFAULT_CONTROLLER);  // invalid URL, so home/index
        if (!file_exists($controllerFile)) {

          /**
           * we are at rock bottom serve up the default controller
           */
          $controllerFile = sprintf('%s/controller/%s.php', __DIR__, config::$DEFAULT_CONTROLLER);  // invalid URL, so system home/index
          if (self::$debug) logger::debug(sprintf('<checking for system default controller (deep)> %s', __METHOD__));
          $this->defaultController = __NAMESPACE__  . '\\controller\\' . config::$DEFAULT_CONTROLLER;
        } else {

          if (self::$debug) logger::debug(sprintf('<default controller> %s', __METHOD__));
        }
      } else {

        if (self::$debug) logger::debug(sprintf('<default controller> %s', __METHOD__));
      }

      if ($this->url_controller != '') {

        if (self::$debug) logger::debug(sprintf('<bumped controller => action> %s', __METHOD__));
        $this->url_parameter_3 = $this->url_parameter_2;
        $this->url_parameter_2 = $this->url_parameter_1;
        $this->url_parameter_1 = $this->url_action;
        $this->url_action = $this->url_controller;  // bump
      }

      $this->url_controller = $this->defaultController;
    }

    if (!file_exists($controllerFile)) {
      logger::info(sprintf('<connot locate controller %s> %s', $controllerFile, __METHOD__));
      throw new CannotLocateController($controllerFile);
    }

    require $controllerFile;
  }

  protected function _isPublicFile(): bool {

    $request = new ServerRequest;
    $_url = trim($request->getPath(), '/. ');

    // $_url = trim(self::Request()->getUrl(), '/. ');
    $extensions = [
      'png',
      'ico',
      'jpg',
      'jpeg',
      'gif',
      'css',
      'js',
      'orf',
      'eot',
      'svg',
      'ttf',
      'woff',
      'woff2',
      'map',
      'json',
      'txt',
      'xml',
      'html'
    ];
    if (preg_match('/\.(' . implode('|', $extensions) . '?)(\?.*)?$/i', $_url)) {
      // if (preg_match('/\.(png|ico|jpg|jpeg|gif|css|js|orf|eot|svg|ttf|woff|woff2|map|json|txt|xml|html?)(\?.*)?$/i', $_url)) {

      /**
       * You are only here because
       *	the file was NOT found in the <webroot>
       *	this may be a public document
       */

      // remove the tail after ?
      // not required - because $_url is derived from a psr7 request
      // $_url = preg_replace('@(\?.*)?$@', '', $_url);

      // check for any .. in the string, which could lead to a parent folder
      if (strpos($_url, '..') !== false)
        throw new SecurityException;

      /**
       * sanitize, noting that it may have / in the string,
       * and that's ok because leading /. have been removed
       */
      $_url = preg_replace('@[^a-zA-Z0-9\_\-\./]@', '', $_url);
      if ($this->_publicFile($_url)) return true;
    }

    return false;
  }

  protected function _publicFile($_url): bool {

    $debug = false;
    // $debug = true;

    if (!$_url) return false;

    $_file = sprintf('%s/app/public/%s', $this->rootPath, $_url);
    $request = new ServerRequest;

    if ($debug) logger::debug(sprintf('<looking for :: %s> %s', $_file, __METHOD__));
    if (file_exists($_file)) {

      $this->url_served = strings::url($request->getUrl(), $protcol = true);
      $this->_serve($_file);
      return true;
    }

    $_file = sprintf('%s/public/%s', $this->rootPath, $_url);
    if ($debug) logger::debug(sprintf('<looking for :: %s> %s', $_file, __METHOD__));
    if (file_exists($_file)) {

      logger::deprecated(sprintf('DEPRECATED FILE LOCATION :: %s', $_file));
      logger::deprecated(sprintf('Please use app/public :: %s', $_file));
      $this->url_served = strings::url($request->getUrl(), $protcol = true);
      $this->_serve($_file);
      return true;
    }

    /**
     * system level public document
     * e.g. vendor/bravedave/dvc/src/bravedave/public/css/font-awesome.min.css
     */

    $_file = sprintf('%s/public/%s', dirname(__DIR__), $_url);
    if ($debug) logger::debug(sprintf('<vendor :: %s> %s', $_file, __METHOD__));
    if (file_exists($_file)) {

      $this->url_served = strings::url($request->getUrl(), $protcol = true);
      $this->_serve($_file);
      return true;
    }

    return false;
  }

  protected function _serve($path) {

    if (self::$debug) \sys::$debug = true;
    Response::serve($path);

    return $this;
  }

  protected function _splitUrl() {

    $request = new ServerRequest;
    $segments = $request->getSegments();
    // logger::dump($segments);

    if ($segments) $this->url_controller = array_shift($segments);
    if ($segments) $this->url_action = array_shift($segments);
    if ($segments) $this->url_parameter_1 = array_shift($segments);
    if ($segments) $this->url_parameter_2 = array_shift($segments);
    if ($segments) $this->url_parameter_3 = array_shift($segments);

    return $this;

    /*---- ----[deprecate below]---- ---- */

    /**
     * Get and split the URL
     */
    // $url = self::Request()->getUrl();
    // if (self::Request()->ReWriteBase() != '' && '/' . $url == self::Request()->ReWriteBase()) {
    //   if (self::$debug) logger::debug(sprintf('ReWriteBase = %s', Request::get()->ReWriteBase()));
    //   $url = '';
    // }

    // if ($url != "") {
    //   if (self::$debug) logger::debug('Url: ' . $url);

    //   // split URL
    //   $url = self::Request()->getSegments();

    //   // Put URL parts into according properties
    //   $this->url_controller = self::Request()->getSegment(0);
    //   $this->url_action = self::Request()->getSegment(1);
    //   $this->url_parameter_1 = self::Request()->getSegment(2);
    //   $this->url_parameter_2 = self::Request()->getSegment(3);
    //   $this->url_parameter_3 = self::Request()->getSegment(4);

    //   // turn debug on if you have problems with the URL
    //   if (self::$debug) logger::debug('Controller: ' . $this->url_controller);
    //   if (self::$debug) logger::debug('Action: ' . $this->url_action);
    //   if (self::$debug) logger::debug('Parameter 1: ' . $this->url_parameter_1);
    //   if (self::$debug) logger::debug('Parameter 2: ' . $this->url_parameter_2);
    //   if (self::$debug) logger::debug('Parameter 3: ' . $this->url_parameter_3);
    // }

    // return $this;
  }

  protected function checkDB() {
    /**
     * checkDB is called during __construct
     *
     * use this method to run database checks
     *
     */
  }

  public function app_executed() {
    return $this->_app_executed;
  }

  public function action() {
    return self::Request()->getActionName();
  }

  public function addPath($path) {
    $this->paths[] = $path;
  }

  public function controller() {

    if (is_string($this->url_controller)) return $this->url_controller;
    if (isset($this->url_controller)) return $this->url_controller->name;
    return '';
  }

  public function countVisit() {
    /**
     * countVisit is called during __construct
     *
     * use this method to gather statistics
     *
     */
  }

  public function dbi(): sqlite\db|dbi|null {

    return \sys::dbi();
  }

  public function getPaths() {

    return $this->paths;
  }

  public function getRootPath() {

    return isset($this)  ?
      $this->rootPath :
      self::app()->getRootPath();
  }

  public function getInstallPath() {

    return (realpath(__DIR__ . '/../../'));  // parent of parent
  }

  public function getVendorPath() {

    $path = dirname($this->getInstallPath());
    if (file_exists($path . '/vendor')) return $path . '/vendor';

    if (preg_match('@vendor/bravedave/dvc@', $path)) {

      $path = realpath(preg_replace('@bravedave/dvc.*@', '', $path));
      if (file_exists($path)) return $path;
    }

    return '';
  }

  public function return_url() {

    return ($this->url_served);
  }

  protected static $_loaded_fallback = false;
  static function load_dvc_autoloader_fallback() {
    if (!self::$_loaded_fallback) {

      self::$_loaded_fallback = true;

      spl_autoload_register(function ($class) {

        if ($lib = realpath(implode([
          dirname(__DIR__),
          DIRECTORY_SEPARATOR,
          'fallback',
          DIRECTORY_SEPARATOR,
          str_replace('\\', '/', $class),
          '.php'
        ]))) {

          include_once $lib;
          load::logger(sprintf('lib: %s', $lib));
          return true;
        }
        return false;
      });
    }
  }

  static function run($dir = null) {

    if (is_null($dir)) {

      if (method_exists('\application', 'startDir')) {

        $app = new \application(\application::startDir());
      } else {

        throw new MissingRootPath;
      }
    } else {

      $app = new application($dir);
    }
  }
}

application::load_dvc_autoloader_fallback();
