<?php
/*
 * Copyright (c) 2025 David Bray
 * Licensed under the MIT License. See LICENSE file for details.
 *
 * This is the "base controller class". All other "real" controllers extend this class.
*/

namespace bravedave\dvc;

use bravedave\dvc\esse\modal;
use config, currentUser, strings;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use RuntimeException;

/**
 * Base Controller Class
 *
 * This abstract class serves as the foundation for all controllers in the application.
 * It provides common functionality such as database access, request handling,
 * view rendering, and authorization mechanisms.
 *
 * @package bravedave\dvc
 * @property bool $authorized Indicates if the user is authorized (American spelling).
 * @property bool $authorised Indicates if the user is authorized (British spelling).
 * @property bool $CheckOffline Determines if offline checks should be performed.
 * @property sqlite\db|dbi|null $db Database connection instance.
 * @property string $name The name of the controller, defaults to 'home'.
 * @property Request $Request The current HTTP request object.
 * @property ServerRequest $ServerRequest The server-side request object.
 * @property string $rootPath The root path of the application.
 * @property string $defaultController The default controller name, defaults to 'home'.
 * @property string $title The title of the current page.
 * @property bool $debug Enables or disables debug mode.
 * @property object $data Arbitrary data object for use in views.
 * @property bool $RequireValidation Determines if validation is required for the controller.
 * @property bool $Redirect_OnLogon Determines if a redirect should occur after logon.
 * @property string|null $label A label for the controller, defaults to the class name.
 * @property string|null $manifest Path to the manifest file, if any.
 * @property string $route The current route being handled.
 * @property array $viewPath Additional paths to search for views.
 * @property array $_viewPathsVerified Cached verified view paths.
 * @property application|null $_application The application instance.
 * @property string $url The base URL for the controller.
 *
 * @method static application application(?application $app = null) Get or set the application instance.
 * @method void index() Default method for handling requests.
 * @method void logout() Logs out the current user and redirects.
 * @method void logoff() Alias for logout().
 * @method void page404() Renders a 404 Not Found page.
 * @method object render(array $params) Builds and returns the page object for the given parameters.
 * @method $this load(string $viewName, ?string $controller = null, array $options = []) Loads a view.
 * @method bool hasView(string $viewName, ?string $controller = null) Checks if a view exists.
 * @method void authorize() Handles user authorization.
 * @method void before() Placeholder for child classes to execute logic before handling requests.
 * @method void serviceWorker() Serves the service worker script.
 * @method void fetchWorker() Serves the fetch worker script.
 */
abstract class controller {
  public $authorized = false;
  public $authorised = false;
  public $CheckOffline = true;

  public sqlite\db|dbi|null $db = null;

  public $name = 'home';

  /** @disregard P1132 */
  public $timer = null;
  public $rootPath  = '';
  public $defaultController = 'home';
  public $title = '';
  public $debug = false;

  protected Request $Request;
  protected ServerRequest $ServerRequest;

  /** @disregard P1132 */
  protected $data;
  protected $RequireValidation = true;
  protected $Redirect_OnLogon = false;

  /** @disregard P1132 */
  protected $label = null;

  /** @disregard P1132 */
  protected $manifest = null;
  protected $route = '/';

  protected $viewPath = [];

  /** @disregard P1132 */
  protected static $_application = null;

  static function application(application|null $app = null): application|null {

    if ($app) self::$_application = $app;
    return self::$_application;
  }

  /** @disregard P1132 */
  static $url;

  /** @var string Path to the default "view not found" file. */
  const viewNotFound = __DIR__ . '/views/not-found.md';

  /** @disregard P1132 */
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
    $this->ServerRequest = new ServerRequest;

    if ($this->RequireValidation) {

      if ($this->debug) logger::debug(sprintf('checking authority :: %s', __METHOD__));
      $this->authorised = currentUser::valid();
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
        $this->authorised = currentUser::valid();
        if ($this->debug) logger::debug(sprintf('checking authority :: %s :: %s', ($this->authorised ? 'private' : 'public'), __METHOD__));

        if (!($this->authorised)) $this->authorize();
      }
    }

    if ($this->CheckOffline) {

      if ($this->authorised) {

        if (!currentUser::isadmin()) {

          if ((new \dao\state)->offline()) Response::redirect(strings::url('offline'));
        }
      }
    }

    $this->authorized = $this->authorised;  // american spelling accepted (doh)
    $this->data = (object)[];

    // if ($this->RequireValidation) {

    //   if ($this->access_control()) {

    //     $this->before();
    //   } else {

    //     Response::redirect(strings::url());  // The user is $authorised, but denied access by their _acl
    //     exit;
    //   }
    // } else {

    //   $this->before();  // no acl required for anon access
    // }
  }

  public function __invoke(string $name = ''): bool {

    $this->name = $name;

    self::$url = strings::url($name . '/');
    if ($this->debug) logger::debug(self::$url);

    return $this->middleware(); // continue processing
  }

  protected function _delete($id = 0) {
    /**
     * respond to a delete request
     * @param int $id
     * @return void
     *
     * not sure I will ever use this, just a placeholder
     */

    // logger::info(sprintf('<DELETE %s is not implemented> %s', $id, logger::caller()));
    json::nak('delete is not implemented'); // default response
  }

  protected function _getView($viewName = 'index', ?string $controller = null, $logMissingView = true): string {

    if (is_null($controller)) $controller = $this->name;

    $_paths = handler::getViewPaths($controller, static::application()->getRootPath(), $this->viewPath);
    foreach ($_paths as $_path) {

      if ($view = handler::viewPath(implode(DIRECTORY_SEPARATOR, [rtrim($_path, '/'), $viewName]))) {

        return $view;
      }
    }

    if (class_exists('dvc\theme\view', /* autoload */ false)) {

      logger::info(sprintf('<deprecation warning - themed view will be removed> %s', logger::caller()));
      if ($altView = '\dvc\theme\view'::getView($viewName)) return $altView;
    }

    $_paths = handler::getSystemViewPaths($controller);
    foreach ($_paths as $_path) {

      if ($view = handler::viewPath(implode(DIRECTORY_SEPARATOR, [rtrim($_path, '/'), $viewName]))) {

        return $view;
      }
    }

    $readmes = [
      implode(DIRECTORY_SEPARATOR, [dirname(static::application()->getRootPath()), 'README.md']),
      implode(DIRECTORY_SEPARATOR, [dirname(dirname(dirname(__DIR__))), 'README.md'])
    ];

    // find case insensitive match in the array that matches the view name, if any
    $match = array_filter($readmes, fn($r) => strcasecmp($r, $viewName) === 0);

    if ($match) return reset($match);  // one exception

    if ($logMissingView && 'dvc\_controller/hasView' != logger::traceCaller()) {

      /*-- --[ not found - here is some debug stuff ]-- --*/
      logger::trace(sprintf('view not found : %s (%s) : %s', $viewName, logger::traceCaller(), __METHOD__));
    }

    return self::viewNotFound;
  }

  protected function _index() {

    $this->page404();
  }

  /** @disregard P1132 */
  protected function _render($view) {

    $a = (array)$view;
    array_walk($a, fn($v) => $this->load($v));
  }

  #[\Deprecated('Use handler::viewPath instead')]
  protected function _viewPath(string $path): string {

    return handler::viewPath($path);

    // if (preg_match('/\.(php|md)$/', $path)) {    // extension was specified
    //   if (\file_exists($path)) {
    //     if ($this->debug) logger::debug(sprintf('found view (specific) : %s :: %s', $path, __METHOD__));
    //     return $path;
    //   }
    // }

    // /**
    //  * first look for a php (.php) view, then a markdown (.md)
    //  */
    // if (file_exists($view = sprintf('%s.php', $path))) {  // php
    //   if ($this->debug) logger::debug(sprintf('found view (php) : %s :: %s', $view, __METHOD__));
    //   return $view;
    // }

    // if (file_exists($view = sprintf('%s.md', $path))) {  // md
    //   if ($this->debug) logger::debug(sprintf('found view (md) : %s :: %s', $view, __METHOD__));
    //   return $view;
    // }

    // return '';
  }

  protected function __tinyserve__(string $lib) {

    $_tinyDir = sprintf(
      '%s/tinymce/tinymce/',
      static::application()->getVendorPath(),
    );

    $uri = $this->Request->getUri();
    if (preg_match('/(\.min\.css|\.css)$/', $uri)) {

      $file = preg_replace('@^(js|assets)/tinymce[5?]/@', '', $uri);
      // logger::info(sprintf('<%s> %s', $file, __METHOD__));

      $_f = implode(DIRECTORY_SEPARATOR, [
        $_tinyDir,
        $file
      ]);

      file_exists($_f) ?
        Response::serve($_f) :
        logger::info(sprintf('<error serving %s> %s', $file, __METHOD__));
    } elseif (preg_match('/(content\.min\.css|content\.css)$/', $uri)) {

      // this loop is probably deprecated

      $_f = sprintf(
        '%s/tinymce/tinymce/skins/content/default/content.min.css',
        static::application()->getVendorPath(),
        $lib
      );

      file_exists($_f) ?
        Response::serve($_f) :
        logger::info(sprintf(
          '<error serving lib tinymce.css> <%s> %s',
          dirname(static::application()->getVendorPath()),
          logger::caller()
        ));
    } elseif (userAgent::isMobileDevice()) {

      jslib::tinyserve('tiny-imap-mobile', 'autolink,lists');
    } else {

      jslib::tinyserve('tiny-imap', 'autolink,paste,lists,table,image,imagetools,link,spellchecker');
    }
  }

  protected function access_control() {

    // warning - don't impose an access_control of FALSE on the home page !
    // logger::info(sprintf('<default access control> %s', logger::caller()));
    return true;
  }

  protected function authorize() {

    if ($this->isPost()) {

      $action = $this->getPost('action');
      if ($action == '-system-logon-' && auth::ImapAuthEnabled()) {

        if (handler::authorizeIMAP()) {

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
      $p->late['logon'] = '<script>(_ => _.ready(() => _.get.modal(_.url(\'logon/form\'))))( _brayworth_);</script>';
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

  #[\Deprecated]
  protected function dbEscape(string $s) {
    /**
     * Escape a string for inclusing in an SQL
     * Command using the default data adapter
     */

    if (is_null($this->db)) return $s;
    return $this->db->escape($s);
  }

  #[\Deprecated]
  protected function dbResult($query) {

    /**
     * Return a SQL Data Result using
     * the default data adapter
     */
    logger::deprecated(sprintf('<%s is not implemented>', __METHOD__));

    // if (is_null($this->db)) return false;
    // return $this->db->result($query);
  }

  protected function getParam($name = '', $default = false) {

    if (is_null($this->Request)) return FALSE;
    return $this->Request->getParam($name, $default);
    // return $this->ServerRequest->getQueryParam($name, $default);
  }

  protected function getPost($name = '', $default = false) {

    return $this->Request ? $this->Request->getPost($name, $default) : false;
  }

  protected function hasPost($name = ''): bool {

    return $this->Request ? $this->Request->hasPost($name) : false;
  }

  protected function isDelete(): bool {

    return $this->Request ? $this->Request->isDelete() : false;
  }

  protected function isPost(): bool {

    if (is_null($this->Request)) return false;
    return $this->Request->isPost();
  }

  protected function middleware(array $middlewares = []): bool {

    foreach ($middlewares as $middleware) {

      if (is_callable($middleware)) {

        $result = call_user_func($middleware);
        if ($result === false) {

          return false;
        }
      }
    }

    if ($this->RequireValidation) {

      if (!$this->access_control()) {

        return false;  // The user is $authorised, but denied access by their _acl
      }
    }

    $this->before();
    return true; // continue processing
  }

  #[\Deprecated('use $this->_getView() instead')]
  protected function getView($viewName = 'index', ?string $controller = null, $logMissingView = true): string {

    return $this->_getView($viewName, $controller, $logMissingView);
  }

  protected function hasView($viewName = 'index', $controller = null) {

    return $this->_getView($viewName, $controller, $logMissingView = false) != self::viewNotFound;
  }

  protected function load($viewName = 'index', $controller = null, array $options = []) {

    $view = $this->_getView($viewName, $controller);
    if (substr_compare($view, '.md', -3) === 0) {

      if ($this->debug) logger::debug(sprintf('it\'s an md ! :: %s', __METHOD__));

      $fc = file_get_contents($view);
      $mdo = [
        'allow_unsafe_links' => $options['allow_unsafe_links'] ?? false,
        'html_input' => $options['html_input'] ?? 'strip',
        'heading_permalink' => [
          'html_class' => 'heading-permalink',  // Optional: Add a CSS class
          'id_prefix' => 'content',            // Optional: Add a prefix to IDs
          'insert' => 'before',                 // Optional: Insert the permalink before or after the heading
          'symbol' => '',                 // Optional: Insert the permalink before or after the heading
        ],
        'footnote' => [
          'backref_class'      => 'footnote-backref',
          'backref_symbol'     => '↩',
          'container_add_hr'   => true,
          'container_class'    => 'footnotes',
          'ref_class'          => 'footnote-ref',
          'ref_id_prefix'      => 'fnref:',
          'footnote_class'     => 'footnote',
          'footnote_id_prefix' => 'fn:',
        ],
      ];

      if ($options['renderer'] ?? false) $mdo['renderer'] = $options['renderer'];

      $converter = new GithubFlavoredMarkdownConverter($mdo);
      $environment = $converter->getEnvironment();
      $environment->addExtension(new HeadingPermalinkExtension);
      printf('<div class="markdown-body">%s</div>', $converter->convert($fc));
    } else {

      $this->protectedLoad($view, (array)$this->data);
    }

    return $this;  // chain
  }

  /**
   * @param string $_do_not_ever_create_a_variable_with_this_name_lol_
   * @param array<string, mixed> $data
   *
   * @return void
   */
  protected function protectedLoad(string $_do_not_ever_create_a_variable_with_this_name_lol_, array $data): void {

    // https://www.php.net/manual/en/function.func-get-arg.php#124846
    extract($data);
    include func_get_arg(0);
  }

  /** @disregard P1132 */
  #[\Deprecated]
  protected function loadView($name, $controller = null) {

    return $this->load($name, $controller);  // that's a chain
  }

  #[\Deprecated]
  protected function modalError($params = []) {

    $options = array_merge([
      'class' => 'modal-sm',
      'header-class' => 'text-white bg-danger py-2',
      'text' => 'error msg',
      'title' => 'Error'
    ], $params);

    // if (!isset($this->data)) $this->data = (object)[];

    // $this->data->text = $options['text'];
    // $this->modal($options);

    modal::alertSM([
      'title' => $options['title'],
      'text' => $options['text']
    ]);

    return $this;  // chain
  }

  #[\Deprecated]
  protected function modal($params = []) {

    logger::trace(sprintf('deprecated : %s', logger::caller()));

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

  #[\Deprecated]
  protected function page(array $params) {

    $defaults = [
      'title' => $this->title,
    ];

    $options = array_merge($defaults, $params);
    return handler::page($options);

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

    $request = new ServerRequest;
    $action = $request('action');

    return match ($action) {

      'send-test-message' => push::test(currentUser::id()) ? json::ack($action) : json::nak($action),
      'subscription-delete' => handler::subscriptionDelete($request),
      'subscription-save' => handler::subscriptionSave($request),
      default => json::nak($action)
    };
  }

  /** @disregard P1132 */
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

    $p = handler::page($options);

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

  /**
   *
   * wraps the page in <html><body> tags and loads the bootstrap5 css/js
   *
   * @param mixed $params
   * @return void
   */
  protected function renderBS5($params): void {

    $aside = config::index_set;
    if ($data = $this->data ?? false) {

      $aside = $data->aside ?? config::index_set;
      $this->data->bootstrap = 5;
    }

    $aside = (array)$aside;

    $options = array_merge([
      'aside' => fn() => array_walk($aside, fn($_) => $this->load($_)),
      'bodyClass' => '',
      'css' => [],
      'footer' => fn() => $this->load('footer'),
      'late' => [],
      'left-layout' => 'yes' == (currentUser::option('enable-left-layout') || 'left' == config::$PAGE_LAYOUT),
      'main' => fn() => '&nbsp;',
      'meta' => [],
      'navbar' => fn() => $this->load(config::navbar_default),
      'scripts' => [],
      'toastui' => false,
    ], $params);

    if ($options['toastui']) {

      $options['css'][] = sprintf('<link rel="stylesheet" href="%s">', strings::url('assets/toastui/css'));
      $options['scripts'][] = sprintf('<script src="%s"></script>', strings::url('assets/toastui/js'));
    }

    $twentyFive = false;
    // $twentyFive = true;
    if ($twentyFive) {

      $options['footer'] = fn() => $this->load('footer25');
      $page = $options['page'] ?? (esse\page25::bootstrap());
    } else {

      $page = $options['page'] ?? (esse\page::bootstrap());
    }

    // page can be a class string for an invokable or non-invokable page object
    if (is_string($page)) {

      $instance = new $page;
      $page = is_callable($instance) ? $instance() : $instance;
    }

    array_walk($options['css'], fn($_) => $page->css[] = preg_match('/^<(link|style)/', $_) ? $_ : sprintf('<link rel="stylesheet" href="%s">', $_));
    array_walk($options['scripts'], fn($_) => $page->scripts[] = preg_match('/^<script/', $_) ? $_ : sprintf('<script src="%s"></script>', $_));

    array_walk($options['late'], fn($_) => $page->late[] = $_);
    array_walk($options['meta'], fn($_) => $page->meta[] = $_);

    if ($options['bodyClass']) $page->bodyClass = $options['bodyClass'];

    $crumbs = fn() => null;
    if ($options['breadcrumb'] ?? null) {

      $crumbs = function () use ($options) {

        print '<nav aria-label="breadcrumb"><ol class="breadcrumb">';

        array_walk($options['breadcrumb'], function (breadcrumb $crumb) {

          if ($crumb->url ?? null) {

            printf(
              '<li class="breadcrumb-item"><a href="%s">%s</a></li>',
              $crumb->url,
              $crumb->label
            );
          } else {

            printf('<li class="breadcrumb-item">%s</li>', $crumb->label);
          }
        });

        print '</ol></nav>';
      };
    }

    $page
      ->head($this->title)
      ->body();

    if ($options['navbar']) $page->then($options['navbar']);

    if ($options['left-layout']) {

      if ($options['aside']) $page->aside()->then($options['aside']);
      $page->main()->then($crumbs)->then($options['main']);
    } else {

      $page->main()->then($crumbs)->then($options['main']);
      if ($options['aside']) $page->aside()->then($options['aside']);
    }

    $page->footer()->then($options['footer']);
  }

  #[\Deprecated]
  protected function SQL($query) {

    /**
     * Perform an SQL Command using
     * the default data adapter
     */
    logger::deprecated(sprintf('<%s is not implemented>', __METHOD__));
    // if (is_null($this->db)) return false;
    // return $this->db->SQL($query);
  }

  public function index() {

    $args = [];
    $i = func_num_args();
    if ($i > 0) $args = func_get_args();

    if ($this->isPost()) {

      $this->postHandler();
    } elseif ($this->isDelete()) {

      if ($i > 1) {

        /** @disregard P1119 */
        $this->_delete($args[0], $args[1]);
      } elseif ($i == 1) {

        $this->_delete($args[0]);
      } else {

        $this->_delete();
      }
    } elseif ($this->manifest) {

      handler::offManifest($this->manifest, self::application()::Request()->getUrl());
    } else {

      if ($i > 1) {

        /** @disregard P1119 */
        $this->_index($args[0], $args[1]);
      } elseif ($i == 1) {

        /** @disregard P1119 */
        $this->_index($args[0]);
      } else {

        $this->_index();
      }
    }
  }

  public function js(string $lib = '') {

    $res = match ($lib) {
      'hooks', 'hooks.module.js' => Response::serve(__DIR__ . '/js/preact/hooks.module.js'),
      'hooks.module.js.map' => Response::serve(__DIR__ . '/js/preact/hooks.module.js.map'),
      'htm', 'htm.module.js' => Response::serve(__DIR__ . '/js/preact/htm.module.js'),
      'importmap' => Response::serve(__DIR__ . '/js/preact/importmap.json'),
      'modal' => Response::serve(__DIR__ . '/js/modules/modal.js'),
      'preact', 'preact.module.js' => Response::serve(__DIR__ . '/js/preact/preact.module.js'),
      'preact.module.js.map' => Response::serve(__DIR__ . '/js/preact/preact.module.js.map'),
      'tiny8' => Response::serve(__DIR__ . '/js/modules/tiny8.js'),
      'tinymce', 'tinymce5' => $this->__tinyserve__($lib),
      default => 99
    };

    return (int)$res < 99;
  }

  public function logout() {

    session::destroy(__METHOD__);
    Response::redirect();
    header('HTTP/1.1 401 Unauthorized');
  }

  public function logoff() {

    $this->logout();
  }

  public function errorTest() {

    throw new RuntimeException;
  }

  public function page404() {

    header('HTTP/1.0 404 Not Found');
    $this->renderBS5([
      'title' => '404 Not Found',
      'content' => 'not-found'
    ]);
  }

  public function fetchWorker() {

    $worker = __DIR__ . '/js/fetchWorker.js';
    Response::serve($worker);
  }

  public function serviceWorker() {

    if (\class_exists('Minishlink\WebPush\VAPID')) {

      push::serviceWorker();
    }
  }
}
