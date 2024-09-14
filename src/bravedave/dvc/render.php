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

use config, currentUser, strings;
use League\CommonMark\GithubFlavoredMarkdownConverter;

trait render {

  protected $viewPath = [];
  protected $_viewPathsVerified = [];

  const viewNotFound = __DIR__ . '/views/not-found.md';

  protected function _getSystemViewPaths(?string $controller = null): array {

    $a = [];
    if (controller\docs::class == $controller) {

      $a[] = implode(DIRECTORY_SEPARATOR, [__DIR__, 'views', 'docs']);
    }

    $a[] = implode(DIRECTORY_SEPARATOR, [__DIR__, 'views']);

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

  protected function _viewPath(string $path): string {

    if (preg_match('/\.(php|md)$/', $path)) {    // extension was specified

      if (\file_exists($path)) return $path;
    }

    /* first look for a php (.php) view, then a markdown (.md) */
    if (file_exists($view = sprintf('%s.php', $path))) return $view;
    if (file_exists($view = sprintf('%s.md', $path))) return $view;
    return '';
  }

  protected function getView($viewName = 'index', ?string $controller = null, $logMissingView = true): string {

    if (is_null($controller)) $controller = $this->name;

    $_paths = $this->_getViewPaths($controller);
    foreach ($_paths as $_path) {

      if ($view = $this->_viewPath(implode(DIRECTORY_SEPARATOR, [rtrim($_path, '/'), $viewName]))) {

        return $view;
      }
    }

    if (class_exists('dvc\theme\view', /* autoload */ false)) {

      if ($altView = '\dvc\theme\view'::getView($viewName)) return $altView;
    }

    $_paths = $this->_getSystemViewPaths($controller);
    foreach ($_paths as $_path) {

      if ($view = $this->_viewPath(implode(DIRECTORY_SEPARATOR, [rtrim($_path, '/'), $viewName]))) {

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
      sys::trace(sprintf('view not found : %s (%s) : %s', $viewName, \sys::traceCaller(), __METHOD__));
    }

    return self::viewNotFound;
  }

  protected function hasView($viewName = 'index', $controller = null): bool {

    return $this->getView($viewName, $controller, $logMissingView = false) != self::viewNotFound;
  }

  protected function load($viewName = 'index', $controller = null, array $options = []) {

    $view = $this->getView($viewName, $controller);
    if (substr_compare($view, '.md', -3) === 0) {

      if ($this->debug) logger::debug(sprintf('it\'s an md ! :: %s', __METHOD__));

      $fc = file_get_contents($view);
      $mdo = [
        'html_input' => $options['html_input'] ?? 'strip',
        'allow_unsafe_links' => $options['allow_unsafe_links'] ?? false,
      ];
      $converter = new GithubFlavoredMarkdownConverter($mdo);
      printf('<div class="markdown-body">%s</div>', $converter->convert($fc));
      // printf('<div class="markdown-body">%s</div>', \Parsedown::instance()->text($fc));
    } else {

      require($view);
    }

    return $this;  // chain
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
    ], $params);

    $page = $options['page'] ?? (esse\page::bootstrap());

    array_walk($options['css'], fn($_) => $page->css[] = preg_match('/^<link/', $_) ? $_ : sprintf('<link rel="stylesheet" href="%s">', $_));
    array_walk($options['scripts'], fn($_) => $page->scripts[] = preg_match('/^<script/', $_) ? $_ : sprintf('<script src="%s"></script>', $_));

    array_walk($options['late'], fn($_) => $page->late[] = $_);
    array_walk($options['meta'], fn($_) => $page->meta[] = $_);

    if ($options['bodyClass']) $page->bodyClass = $options['bodyClass'];

    $page
      ->head($this->title)
      ->body()->then($options['navbar']);

    if ($options['left-layout']) {

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
}
