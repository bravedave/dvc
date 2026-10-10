<?php
/*
 * Copyright (c) 2026 David Bray
 * Licensed under the MIT License. See LICENSE file for details.
*/

namespace bravedave\dvc;

use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\GithubFlavoredMarkdownConverter;

/**
 * Loads a view by name and prints it.
 *
 * Usage:
 *   $view = new view($data);            // $data keys are extracted into the view scope
 *   $view->wrap = ['card', 'p-3'];      // optional classes, each wrapped as a div
 *   $view->load('todo/matrix');         // name without extension; .php then .md
 *   $view->load('/abs/path/file');      // absolute: tried as given, then under the search roots
 *   $view('todo/matrix');               // same as load()
 *
 * Relative names are searched under the root path, the app views, and the dvc views.
 * Names containing '..' or null bytes are refused and logged.
 */
class view {
  public ?array $data = null;
  public $loadName = '?';
  public $title = '';
  public $wrap = [];
  public $debug = false;

  protected $paths = [];
  protected $rootPath = false;

  public function __construct($data = null, array $paths = []) {

    if ($app = application::app()) {

      $this->paths[] = $this->rootPath = $app->getRootPath();

      // had to add these temporarily removed now
      // $this->paths[] = dirname($this->rootPath);
      // $this->paths[] = $app->getVendorPath();

      foreach ($app->getPaths() as $path) {
        $this->paths[] = $path;
      }
    } else {

      $this->paths[] = $this->rootPath = __DIR__;
    }

    array_walk($paths, fn($path) => array_unshift($this->paths, $path));
    $this->data = $data;
  }


  public function __invoke(string $name): bool {

    return $this->load($name);
  }

  public static function instance() {

    return new static;
  }

  protected function _getview(string $name): string {

    /*
    lets start by rejecting any name that can do directory traversal

    ultimately this is a composer installed package
    - we never want to traverse back to root
    - our target limit is the parent of the vendor package
    - an example application would be
      - `/var/www/cms/application/app/application.php`
      - it passes in `/var/www/cms/application/app` as the directory
    - our targets are
      1. the applications folders
        - returned by $app->getRootPath()
      2. this code base
        - `src/bravedave/dvc/views`
   */

    if (str_contains($name, "\0")) {
      logger::error(sprintf('<%s> : rejected, null byte : %s', str_replace("\0", '\0', $name), __METHOD__));
      return '';
    }

    if (in_array('..', preg_split('#[/\\\\]+#', $name), true)) {
      logger::error(sprintf('<%s> : rejected, traversal : %s', $name, __METHOD__));
      return '';
    }

    $absolute = str_starts_with($name, DIRECTORY_SEPARATOR);
    $roots = $absolute
      ? $this->paths
      : [...$this->paths, $this->rootPath . '/app/views', __DIR__ . '/views'];

    foreach ($roots as $path) {

      if (!$path) continue;

      $root = rtrim(realpath($path) ?: $path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
      $candidates = $absolute
        ? [$name, $name . '.php', $name . '.md']
        : [$path . DIRECTORY_SEPARATOR . $name . '.php', $path . DIRECTORY_SEPARATOR . $name . '.md'];

      foreach ($candidates as $candidate) {

        $real = realpath($candidate);
        if ($real && is_file($real) && str_starts_with($real, $root)) {

          if ($this->debug) logger::debug(sprintf('<%s> : found in path : %s', $name, __METHOD__));
          return $real;
        }
      }
    }

    /*
    where we have looked (default conventional install, <install> holds vendor/ and app/)
    first hit wins, each candidate must realpath inside its bounding root

    relative name X (X.php, else X.md), e.g. 'todo/matrix'
      1. <install>/X.php                                   bounded by <install>
      2. addPath() and constructor $paths                  none by default
      3. <install>/app/views/X.php                         bounded by app/views
      4. <install>/vendor/bravedave/dvc/src/bravedave/dvc/views/X.php   bounded by dvc views
      - pass the name without an extension

    absolute name /X
      - tries <install>/X, <install>/X.php and <install>/X.md, then addPath() roots
      - dvc views are not searched, so /X must resolve under <install>
      - because <install> is the bound, vendor/ is reachable through rootPath
    */

    return '';
  }

  protected function _load(string $path): view {

    if (substr_compare($path, '.md', -3) === 0) {

      if ($this->debug) logger::debug(sprintf('<it\'s an md !> %s', __METHOD__));

      $fc = file_get_contents($path);
      $converter = new GithubFlavoredMarkdownConverter([
        'allow_unsafe_links' => false,
        'html_input' => 'strip',
        'heading_permalink' => [
          'html_class' => 'heading-permalink',
          'id_prefix' => 'content',
          'insert' => 'before',
          'symbol' => '',
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
      ]);
      $converter->getEnvironment()->addExtension(new HeadingPermalinkExtension);
      printf('<div class="markdown-body">%s</div>', $converter->convert($fc));
    } else {

      $this->_protectedLoad($path, (array)$this->data);
    }

    return $this;  // chain
  }

  /**
   * @param string $_do_not_ever_create_a_variable_with_this_name_lol_
   * @param array<string, mixed> $data
   *
   * @return void
   */
  protected function _protectedLoad(string $_do_not_ever_create_a_variable_with_this_name_lol_, array $data): void {

    // https://www.php.net/manual/en/function.func-get-arg.php#124846
    extract($data);
    include func_get_arg(0);
  }

  protected function _wrap() {

    if (count((array)$this->wrap)) {

      foreach ((array)$this->wrap as $wrap) {

        if ($wrap) printf('<div class="%s">', $wrap);
      }
    }

    return $this;  // chain
  }

  protected function _unwrap() {
    if (count((array)$this->wrap)) {
      foreach ((array)$this->wrap as $wrap) {
        if ($wrap) printf('</div><!-- wrap:div class="%s" -->', $wrap);
      }
    }

    return $this;  // chain
  }

  /** @disregard P1132 */
  #[\Deprecated]
  public function loadView($name) {

    return $this->load($name);
  }

  public function load(string $name): bool {

    if ($path = $this->_getview($name)) {

      $parts = pathinfo($path);
      $this->loadName = $parts['filename'];
      $this
        ->_wrap()
        ->_load($path)
        ->_unwrap();

      return true;
    }

    if ($this->debug) logger::debug(sprintf('<%s> : NOT found : %s', $name, __METHOD__));
    $this->loadName = $name;

    if (class_exists('dvc\theme\view', /* autoload */ false)) {

      logger::info(sprintf('<deprecation warning - themed view will be removed> %s', logger::caller()));
      if ($themeView = '\dvc\theme\view'::getView($name)) {

        $this
          ->_wrap()
          ->_load($themeView)
          ->_unwrap();

        return true;
      }
    }

    printf('view::%s - not found<br />', $name);
    printf('root::%s<br />', $this->rootPath);
    print '<br />';

    return false;
  }
}
