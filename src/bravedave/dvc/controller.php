<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * This is the "base controller class". All other "real" controllers extend this class.
*/

namespace bravedave\dvc;

use config, strings;

abstract class controller {
  public $authorized = false;
  public $authorised = false;
  public $CheckOffline = true;
  public $db = null;
  public $name = 'home';
  public $Request = null;
  public $timer = null;
  public $rootPath  = '';
  public $defaultController = 'home';
  public $title;
  public $debug = false;

  protected $data;
  protected $RequireValidation = true;
  protected $Redirect_OnLogon = false;
  protected $label = null;
  protected $manifest = null;
  protected $route = '/';

  protected $viewPath = [];
  protected $_viewPathsVerified = [];

  protected static $_application = null;

  static function application(application $app = null): application {

    if ($app) self::$_application = $app;
    return self::$_application;
  }

  static $url;

  const viewNotFound = __DIR__ . '/views/not-found.md';

  public function __construct($rootPath) {
    if ($this->debug) logger::debug(sprintf('__construct :: %s', __METHOD__));
    $this->rootPath = $rootPath;
    $this->title = config::$WEBNAME;
    $this->route = self::application()::route();
    if (is_null($this->label)) {
      $this->label = ucwords(get_class($this));
    }

    /**
     * When a controller is created, open a database connection.
     * There is ONE connection that is used globally.
     */
    $this->db = self::application()::app()->dbi();
    $this->Request = self::application()::Request();

    if ($this->RequireValidation) {

      if ($this->debug) logger::debug(sprintf('checking authority :: %s', __METHOD__));
      $this->authorised = \currentUser::valid();
      if ($this->debug) logger::debug(sprintf('checking authority :: %s :: %s', ($this->authorised ? 'private' : 'public'), __METHOD__));

      if (!($this->authorised)) $this->authorize();
    } elseif ($this->isPost()) {

      /**
       * possibly authentication is not turned on
       * but they have a page that requires validation
       */
      $action = $this->getPost('action');
      if ('-system-logon-' == $action) {

        if ($this->debug) logger::debug(sprintf('checking authority :: %s', __METHOD__));
        $this->authorised = \currentUser::valid();
        if ($this->debug) logger::debug(sprintf('checking authority :: %s :: %s', ($this->authorised ? 'private' : 'public'), __METHOD__));

        if (!($this->authorised)) $this->authorize();
      }
    }

    if ($this->CheckOffline) {

      if ($this->authorised) {

        if (!\currentUser::isadmin()) {

          if ((new \dao\state)->offline()) Response::redirect(strings::url('offline'));
        }
      }
    }

    $this->authorized = $this->authorised;  // american spelling accepted (doh)
    $this->data = (object)[];

    if ($this->RequireValidation) {

      if ($this->access_control()) {

        $this->before();
      } else {

        Response::redirect(strings::url());  // The user is $authorised, but denied access by their _acl
      }
    } else {

      $this->before();  // no acl required for anon access
    }
  }

  protected function _getSystemViewPaths(?string $controller = null): array {
    $a = [];
    if (controller\docs::class == $controller) {

      $a[] = implode(DIRECTORY_SEPARATOR, [
        __DIR__,
        'views',
        'docs'
      ]);
    }

    $a[] = implode(DIRECTORY_SEPARATOR, [
      __DIR__,
      'views'
    ]);

    return $a;
  }

  protected function _getViewPaths(string $controller): array {
    if ($this->_viewPathsVerified) return $this->_viewPathsVerified;

    $_paths = (array)$this->viewPath;
    if ($_dir = realpath(implode(DIRECTORY_SEPARATOR, [$this->rootPath, 'views', $controller]))) {
      $_paths[] =  $_dir;
    }

    if ($_dir = realpath(implode(DIRECTORY_SEPARATOR, [$this->rootPath, 'app', 'views', $controller]))) {
      $_paths[] =  $_dir;
    }

    if ($_dir = realpath(implode(DIRECTORY_SEPARATOR, [$this->rootPath, 'app', 'views']))) {
      $_paths[] =  $_dir;
    }

    $this->_viewPathsVerified = $_paths;

    return $this->_viewPathsVerified;
  }

  protected function _index() {

    $this->page404();
  }

  protected function _offManifest($option = '') {

    if (!$option) $option = 'index.html';
    if ($_manifest_file = realpath(sprintf('%s/asset-manifest.json', $this->manifest))) {

      if ('manifest.json' == $option) {

        $_path = sprintf('%s/%s', $this->manifest, $option);
      } else {

        $_manifest = json_decode(file_get_contents($_manifest_file));
        $_path = false;
        foreach ($_manifest as $_p) {

          if (ltrim($_p, './') == $option) {

            $_path = sprintf('%s/%s', $this->manifest, ltrim($_p, './'));
          }
        }
      }

      if ($_path) {

        if ($_file = realpath($_path)) {

          Response::serve($_file);
        } else {

          printf('%s - file not found', $_path);
          //~ \sys::dump( $_manifest);
        }
      } else {

        printf('%s - not set<br />', $option);
      }
    } else {

      printf('%s - manifest not found', $_manifest_file);
    }

    // if we find a static file serve it, otherwise serve index
  }

  protected function _render($view) {

    foreach ((array)$view as $_) {

      $this->load($_);
    }
  }

  protected function _viewPath(string $path): string {

    if (preg_match('/\.(php|md)$/', $path)) {    // extension was specified
      if (\file_exists($path)) {
        if ($this->debug) logger::debug(sprintf('found view (specific) : %s :: %s', $path, __METHOD__));
        return $path;
      }
    }

    /**
     * first look for a php (.php) view, then a markdown (.md)
     */
    if (file_exists($view = sprintf('%s.php', $path))) {  // php
      if ($this->debug) logger::debug(sprintf('found view (php) : %s :: %s', $view, __METHOD__));
      return $view;
    }

    if (file_exists($view = sprintf('%s.md', $path))) {  // md
      if ($this->debug) logger::debug(sprintf('found view (md) : %s :: %s', $view, __METHOD__));
      return $view;
    }

    return '';
  }

  protected function access_control() {
    // warning - don't impose an access_control of FALSE on the home page !
    return (true);
  }

  protected function authorizeIMAP(): bool {
    $debug = false;
    // $debug = true;

    if ($u = $this->getPost('u')) {
      if ($p = $this->getPost('p')) {

        if (\auth::ImapTest($u, $p)) {
          if ($debug) logger::debug(sprintf('<successful logon for %s> %s', $u, __METHOD__));

          $dao = new \dao\users;
          if (method_exists($dao, 'validatedByIMAP')) {
            return $dao->{'validatedByIMAP'}($u, $p);
          }
        } else {
          if ($debug) logger::debug(sprintf('<unsuccessful logon for %s> %s', $u, __METHOD__));
        }
      }
    }

    return false;
  }

  protected function authorize() {

    if ($this->isPost()) {

      $action = $this->getPost('action');
      if ($action == '-system-logon-' && auth::ImapAuthEnabled()) {

        if ($this->authorizeIMAP()) {

          json::ack($action);
        } else {

          json::nak($action);
        }
        die;
      }
    }

    if (\auth::GoogleAuthEnabled()) {

      if ($this->debug) logger::debug(sprintf('gauth - test :: %s', __METHOD__));
      if (\user::hasGoogleFlag()) {

        \user::clearGoogleFlag();
        if ($this->debug) logger::debug(sprintf('gauth :: %s', __METHOD__));

        Response::redirect(strings::url('auth/request'));
        return;
      }
    }

    if (\userAgent::isGoogleBot()) exit;  // quietly

    if (config::use_inline_logon) {

      $p = new config::$PAGE_TEMPLATE_LOGON('Log On');
      $p->footer = false;
      $p->latescripts[] = '<script>(_ => $(document).ready( () => _.get.modal(_.url(\'logon/form\'))))( _brayworth_);</script>';

      $p->meta[] = '<meta name="viewport" content="initial-scale=1" />';
      $p->header()->content();
    } else {
      if ($this->Redirect_OnLogon) {
        Response::redirect(strings::url(sprintf('logon?referer=%s', $this->Redirect_OnLogon)));
      } else {
        Response::redirect(strings::url('logon'));
      }
    }
    die;
  }

  protected function before() {
    /**
     * Placeholder for use by the child class.
     * This method is called
     * at the end of __construct()
     *
     * avoid replacing the default __construct method - use before instead
     *
     * Inspired by something I read in the fuelPHP documentation
     * this method is called at the end of __construct and can
     * be used to modify the _controller class
     */
  }

  protected function dbEscape($s) {
    /*
		* Escape a string for inclusing in an SQL
		* Command using the default data adapter
		*/

    if (is_null($this->db)) return $s;
    return $this->db->escape($s);
  }

  protected function dbResult($query) {
    /*
		* Return a SQL Data Result using
		* the default data adapter
		*/

    if (is_null($this->db)) return FALSE;
    return $this->db->Result($query);
  }

  protected function getParam($v = '', $default = false) {

    if (is_null($this->Request)) return FALSE;
    return $this->Request->getParam($v, $default);
  }

  protected function getPost($name = '', $default = false) {
    if (is_null($this->Request))
      return (false);

    return $this->Request->getPost($name, $default);
  }

  protected function isPost() {

    if (is_null($this->Request)) return false;
    return $this->Request->isPost();
  }

  protected function getView($viewName = 'index', ?string $controller = null, $logMissingView = true): string {

    if (is_null($controller)) $controller = $this->name;

    $_paths = $this->_getViewPaths($controller);
    foreach ($_paths as $_path) {

      if ($view = $this->_viewPath(implode(DIRECTORY_SEPARATOR, [$_path, $viewName]))) {

        return $view;
      }
    }

    if (class_exists('dvc\theme\view', /* autoload */ false)) {

      if ($altView = '\dvc\theme\view'::getView($viewName)) return $altView;
    }

    $_paths = $this->_getSystemViewPaths($controller);
    foreach ($_paths as $_path) {

      if ($view = $this->_viewPath(implode(DIRECTORY_SEPARATOR, [$_path, $viewName]))) {

        return $view;
      }
    }

    $readme = implode(DIRECTORY_SEPARATOR, [
      dirname(dirname(dirname(__DIR__))),
      'Readme.md'
    ]);

    if ($viewName == $readme) return $readme;  // one exception

    if ($logMissingView && 'dvc\_controller/hasView' != \sys::traceCaller()) {

      /*-- --[ not found - here is some debug stuff ]-- --*/
      \sys::trace(sprintf('view not found : %s (%s) : %s', $viewName, \sys::traceCaller(), __METHOD__));
    }

    return self::viewNotFound;
  }

  protected function hasView($viewName = 'index', $controller = null) {

    return $this->getView($viewName, $controller, $logMissingView = false) != self::viewNotFound;
  }

  protected function load($viewName = 'index', $controller = null) {

    $view = $this->getView($viewName, $controller);
    if (substr_compare($view, '.md', -3) === 0) {

      if ($this->debug) logger::debug(sprintf('it\'s an md ! :: %s', __METHOD__));

      $fc = file_get_contents($view);
      printf('<div class="markdown-body">%s</div>', \Parsedown::instance()->text($fc));
    } else {

      require($view);
    }

    return $this;  // chain
  }

  protected function loadView($name, $controller = null) {

    return $this->load($name, $controller);  // that's a chain
  }

  protected function modalError($params = []) {

    $options = array_merge([
      'class' => 'modal-sm',
      'header-class' => 'text-white bg-danger py-2',
      'text' => 'error msg',
      'title' => 'Error'
    ], $params);

    if (!isset($this->data)) $this->data = (object)[];

    $this->data->text = $options['text'];
    $this->modal($options);

    return $this;  // chain
  }

  protected function modal($params = []) {

    \sys::trace(sprintf('deprecated : %s', __METHOD__));

    $options = array_merge([
      'title' => sprintf('%s Modal', config::$WEBNAME),
      'class' => '',
      'header-class' => 'text-white bg-secondary py-2',
      'load' => false,
      'text' => false,
    ], $params);

    $m = new \dvc\pages\modal([
      'title' => $options['title'],
      'class' => $options['class'],
      'header-class' => $options['header-class'],
    ]);

    $m->open();

    if ($options['load']) {

      foreach ((array)$options['load'] as $_) {

        $this->load($_);
      }
    }

    if ($options['text']) {

      foreach ((array)$options['text'] as $_) {

        print $_;
      }
    }

    return $this;  // chain
  }

  protected function page($params) {

    $defaults = [
      'css' => [],
      'data' => false,
      'footer' => true,
      'latescripts' => [],
      'meta' => [],
      'scripts' => [],
      'title' => $this->title,
      'bodyClass' => false,
      'template' => config::$PAGE_TEMPLATE,
    ];

    $options = array_merge($defaults, $params);

    $p = new $options['template']($options['title']);
    $p->bodyClass = $options['bodyClass'];
    if ('string' == gettype($options['footer'])) {

      $p->footer = true;
      $options['template']::$footerTemplate = $options['footer'];
    } else {

      $p->footer = $options['footer'];
    }

    $p->data = (object)$options['data'];
    if (!(isset($p->data->title))) $p->data->title = $options['title'];

    foreach ($options['css'] as $css) {

      if (preg_match('/^<(link|style)/', $css)) {

        $p->css[] = $css;
      } else {

        $p->css[] = sprintf('<link type="text/css" rel="stylesheet" media="all" href="%s" />', $css);
      }
    }

    foreach ($options['meta'] as $meta) {

      $p->meta[] = $meta;
    }

    foreach ($options['scripts'] as $script) {

      if (preg_match('/^<script/', $script)) {

        $p->scripts[] = $script;
      } else {

        $p->scripts[] = sprintf('<script type="text/javascript" src="%s"></script>', $script);
      }
    }

    /*
		* latescripts are prepended
		* - if something like tinymce is appended after it would be slower
		*/
    foreach ($options['latescripts'] as $script) {

      if (preg_match('/^<script/', $script)) {

        array_unshift($p->latescripts, $script);
      } else {

        array_unshift($p->latescripts, sprintf('<script type="text/javascript" src="%s"></script>', $script));
      }
    }

    return $p;
  }

  protected function postHandler() {

    $action = $this->getPost('action');

    if ('send-test-message' == $action) {

      push::test(\currentUser::id());
    } elseif ('subscription-delete' == $action) {

      if ($endpoint = $this->getPost('endpoint')) {

        $dao = new \dao\notifications;
        $dao->deleteByEndPoint($endpoint);
        json::ack($action);
      } else {

        json::nak($action);
      }
    } elseif ('subscription-save' == $action) {

      if ($json = $this->getPost('json')) {

        $subscription = (object)json_decode($json);

        if (isset($subscription->endpoint) && $subscription->endpoint) {
          $dao = new \dao\notifications;
          if ($dto = $dao->getByEndPoint($subscription->endpoint)) {
            $dao->UpdateByID(['json' => $json], $dto->id);
          } else {
            $dao->Insert([
              'json' => $json,
              'endpoint' => $subscription->endpoint,
              'user_id' => \currentUser::id()

            ]);
          }

          json::ack($action);
        } else {

          json::nak($action);
        }
      } else {

        json::nak($action);
      }
    } else {

      json::nak($action);
    }
  }

  protected function render($params) {

    $defaults = [
      'left-interface' => false,
      'main' => false,
      'main-panel' => false,
      'primary' => false,
      'secondary' => false,
      'sidebar' => false,
      'content' => false,
      'navbar' => '',
    ];

    $options = array_merge($defaults, $params);

    $p = $this->page($options);

    $p->header()
      ->title($options['navbar']);

    if ($options['left-interface']) {

      if ($options['secondary']) {

        $p->secondary();
        $this->_render($options['secondary']);
      }

      if ($options['primary']) {

        $p->primary();
        $this->_render($options['primary']);
      }
    } else {

      if ($options['primary']) {

        $p->primary();
        $this->_render($options['primary']);
      }

      if ($options['secondary']) {

        $p->secondary();
        $this->_render($options['secondary']);
      }
    }

    if ($options['content']) {

      $p->content();
      $this->_render($options['content']);
    }

    if ($options['sidebar']) {
      /*
			Tip 1: You can change the color of the sidebar using: data-color="purple | blue | green | orange | red"
			Tip 2: you can also add an image using data-image tag
			*/
      $more = null;
      if ('lightdash' == config::$THEME) {
        $more = sprintf('data-color="orange" data-image="%s"', strings::url('theme/img/sidebar-5.jpg'));
      } elseif ('material-dashboard' == config::$THEME) {
        $more = sprintf('data-color="rose" data-background-color="black" data-image="%s"', strings::url('theme/img/sidebar-1.jpg'));
      }

      $p->newSection($name = 'sidebar', $class = 'sidebar', $role = 'sidebar', $more = '');
      $this->_render($options['sidebar']);
    }

    if ($options['main-panel']) {

      $p->newSection($name = 'main-panel', $class = 'main-panel', $role = 'main-panel', $more = '');
      $this->_render($options['main-panel']);
    }

    if ($options['main']) {

      $p->newSection($name = 'main', $class = 'main', $role = 'main', $more = '');
      $this->_render($options['main']);
    }

    return $p;
  }

  protected function renderBS5($params): void {

    $aside = config::index_set;
    if ($data = $this->data ?? false) {

      $aside = $data->aside ?? config::index_set;
      $this->data->bootstrap = 5;
    }

    $aside = (array)$aside;

    $options = array_merge([
      'navbar' => fn () => $this->load(config::navbar_default),
      'main' => fn () => '&nbsp;',
      'aside' => fn () => array_walk($aside, fn ($_) => $this->load($_)),
      'footer' => fn () => $this->load('footer'),
      'css' => [],
      'scripts' => []
    ], $params);

    $page = (esse\page::bootstrap());

    array_walk($options['css'], fn ($_) => $page->css[] = preg_match('/^<link/', $_) ? $_ : sprintf('<link rel="stylesheet" href="%s">', $_));
    array_walk($options['scripts'], fn ($_) => $page->scripts[] = preg_match('/^<script/', $_) ? $_ : sprintf('<script src="%s"></script>', $_));

    $page
      ->head($this->title)
      ->body()->then($options['navbar']);

    if ('yes' == currentUser::option('enable-left-layout') || 'left' == config::$PAGE_LAYOUT) {

      if ($options['aside']) $page->aside()->then($options['aside']);
      $page
        ->main()->then($options['main']);
    } else {

      $page
        ->main()->then($options['main']);
      if ($options['aside']) $page->aside()->then($options['aside']);
    }

    $page->footer()->then($options['footer']);
  }

  protected function SQL($query) {

    /**
     * Perform an SQL Command using
     * the default data adapter
     */
    if (is_null($this->db)) return false;
    return $this->db->SQL($query);
  }

  public function index() {

    if ($this->isPost()) {

      $this->postHandler();
    } elseif ($this->manifest) {

      $this->_offManifest(self::application()::Request()->getUrl());
    } else {

      $i = func_num_args();
      if ($i > 0) {

        $args = func_get_args();

        if ($i > 1) {
          $this->_index($args[0], $args[1]);
        } else {
          $this->_index($args[0]);
        }
      } else {

        $this->_index();
      }
    }
  }

  public function js(string $lib = '') {

    if (in_array($lib, ['tinymce', 'tinymce5'])) {

      if (preg_match('/(content\.min\.css|content\.css)$/', $uri = $this->Request->getUri())) {

        $_f = sprintf(
          'tinymce' == $lib ?
            '%s/dvc/public/js/%s/skins/lightgray/content.min.css' :
            '%s/dvc/public/js/%s/skins/content/default/content.min.css',
          self::application()->getInstallPath(),
          $lib
        );

        file_exists($_f) ?
          Response::serve($_f) :
          logger::info('error serving lib tinymce.css');

        // logger::info( sprintf( 'serving lib tinymce %s', $this->Request->getUri()));

      } else {

        if (userAgent::isMobileDevice()) {

          jslib::tinyserve('tiny-imap-mobile', 'autolink,lists');
        } else {

          jslib::tinyserve('tiny-imap', 'autolink,paste,lists,table,image,imagetools,link,spellchecker');
        }
      }
    }
  }

  public function logout() {

    session::destroy(__METHOD__);
    Response::redirect();
    header('HTTP/1.1 401 Unauthorized');
  }

  public function logoff() {

    $this->logout();
  }

  public function init($name = '') {

    self::$url = strings::url($name . '/');
    if ($this->debug) logger::debug(self::$url);
  }

  public function errorTest() {

    throw new \dvc\Exceptions\GeneralException;
  }

  public function page404() {

    header('HTTP/1.0 404 Not Found');
    $this->render([
      'title' => '404 Not Found',
      'content' => 'not-found'
    ]);
  }

  public function serviceWorker() {

    push::serviceWorker();
  }
}
