<?php
/*
 * Copyright (c) 2026 David Bray
 * Licensed under the MIT License. See LICENSE file for details.
 *
 * Replacement Candidate for controller::renderBS5
 *
 * Review findings:
 * - [fixed] Initialize empty data when omitted; PHP views currently throw.
 * - [fixed] jQuery, _brayworth_, icons and import maps are no longer supplied.
 *
 * - Let caller view paths override bundled defaults; preserve caller order.
 * - Templates loaded here use renderer context, not controller context.
 *   Controller-owned load callbacks can preserve legacy template support.
 * - Legacy bodyClass, breadcrumb, page and configured defaults are not carried over.
 *   These compatibility omissions may be intentional for a lightweight model.
 * - Demo coverage is basic; test view overrides and intended module workflows.
*/

namespace bravedave\dvc\esse;

use application;
use bravedave\dvc\config;
use bravedave\dvc\logger;
use bravedave\dvc\resources;
use bravedave\dvc\strings;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use stdClass;

class render6 {
  protected bool $debug = false;
  protected stdClass|array $data;
  protected array $paths = [];
  protected string $rootPath;

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

  protected function _load(string $path): self {

    // $this->debug = getenv('developer') == 'yes';

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
  protected function _protectedLoad(string $_do_not_ever_create_a_variable_with_this_name_lol_, array|null $data): void {

    // https://www.php.net/manual/en/function.func-get-arg.php#124846
    if ($data) extract($data);
    include func_get_arg(0);
  }

  public function __construct(null|stdClass|array $data = null, array $paths = []) {

    // $this->debug = getenv('developer') == 'yes';

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

    $paths[] = __DIR__ . '/render6/';

    array_walk($paths, fn($path) => array_unshift($this->paths, $path));
    $this->data = $data ? $data : [];
  }

  public function __invoke(array $params = []) {

    $options = array_merge([
      'title' => config::$WEBNAME,
      'main' => fn() => null,
      'crumbs' => fn() => null,
      'aside' => fn() => null,
      'footer' => fn() => null,
      'navbar' => fn() => null,
      'left-layout' => true,
      'css' => [],
      'scripts' => [],
      'late' => [],
      'meta' => [],
    ], $params);

    $page = new page;
    $page->asideClass = 'md:col-3 py-3 pb-4 fg-secondary bg-1';
    $page->meta[] = '<meta charset="utf-8">';
    $page->meta[] = '<meta name="viewport" content="width=device-width, initial-scale=1">';

    $path = resources::bootstrap6('css');
    $hash = hash_file('sha384', $path, true);
    $integrity = 'sha384-' . base64_encode($hash);
    $css = strings::url('assets/bootstrap/css/6?v=' . bin2hex($hash));
    $page->css[] = sprintf('<link rel="stylesheet" href="%s" integrity="%s" crossorigin="anonymous">', $css, $integrity);
    $page->css[] = '<style>
      .page { display: flex; flex-direction: column; height: 100vh; height: 100dvh; overflow: hidden; }
      .page > nav, .page > footer { flex: 0 0 auto; }
      .page > div.container-fluid { display: flex; flex-direction: column; flex: 1 1 0; min-height: 0; }
      .page > div.container-fluid > .row { flex: 1 1 0; flex-wrap: nowrap; min-height: 0; }
      .page > div.container-fluid > .row > aside,
      .page > div.container-fluid > .row > main { min-height: 0; min-width: 0; overflow: auto; }
      @media (width < 768px) {
        .page > div.container-fluid > .row { flex-direction: column; }
        .page > div.container-fluid > .row > aside { flex: 1 1 0; }
        .page > div.container-fluid > .row > main { flex: 2 1 0; }
      }
      @media print {
        .page { display: block; height: auto; overflow: visible; }
        .page > div.container-fluid, .page > div.container-fluid > .row { display: block; }
        .page > div.container-fluid > .row > main { overflow: visible; width: 100%; }
        [data-role="content-secondary"] { display: none !important; }
      }
      html {
        scrollbar-gutter: auto !important;
      }
      </style>';

    $icons = strings::url('assets/bootstrap/icons');
    $page->css[] = sprintf('<link rel="stylesheet" href="%s">', $icons);

    $page->scripts[] = sprintf('<script src="%s"></script>', strings::url('assets/jquery/4'));
    $page->scripts[] = sprintf('<script src="%s"></script>', strings::url('assets/brayworth/js'));
    $page->scripts[] = sprintf('<script src="%s"></script>', strings::url('assets/brayworth/dopo'));

    $path = resources::bootstrap6('js');
    $hash = hash_file('sha384', $path, true);
    $integrity = 'sha384-' . base64_encode($hash);
    $js = strings::url('assets/bootstrap/js/6?v=' . bin2hex($hash));
    $page->late[] = sprintf('<script type="module" src="%s" integrity="%s" crossorigin="anonymous"></script>', $js, $integrity);

    array_walk($options['css'], fn($_) => $page->css[] = preg_match('/^<(link|style)/', $_) ? $_ : sprintf('<link rel="stylesheet" href="%s">', $_));
    array_walk($options['scripts'], fn($_) => $page->scripts[] = preg_match('/^<script/', $_) ? $_ : sprintf('<script src="%s"></script>', $_));

    array_walk($options['late'], fn($_) => $page->late[] = $_);
    array_walk($options['meta'], fn($_) => $page->meta[] = $_);

    $page
      ->head($options['title'])
      ->body();

    if ($options['navbar']) $page->then($options['navbar']);

    if ($options['left-layout']) {

      if ($options['aside']) $page->aside()->then($options['aside']);
      $page->main()->then($options['crumbs'])->then($options['main']);
    } else {

      $page->main()->then($options['crumbs'])->then($options['main']);
      if ($options['aside']) $page->aside()->then($options['aside']);
    }

    $page->footer()->then($options['footer']);
  }

  public function demo(): array {

    return [
      'title' => config::$WEBNAME,
      'main' => fn() => $this->load('main6'),
      'crumbs' => fn() => null,
      'aside' => fn() => $this->load('aside6'),
      'footer' => fn() => $this->load('footer6'),
      'navbar' => fn() => $this->load('navbar6'),
      'left-layout' => true,
      'css' => [],
      'scripts' => [],
      'late' => [],
      'meta' => [],
    ];
  }

  public function load(string $name): self {

    if ($path = $this->_getview($name)) {

      $this->_load($path);
      return $this;
    }

    if ($this->debug) logger::debug(sprintf('<%s> : NOT found : %s', $name, __METHOD__));

    if (class_exists('dvc\theme\view', /* autoload */ false)) {

      logger::info(sprintf('<deprecation warning - themed view will be removed> %s', logger::caller()));
      if ($themeView = '\dvc\theme\view'::getView($name)) {

        $this->_load($themeView);

        return $this;
      }
    }

    printf('view::%s - not found<br />', $name);
    printf('root::%s<br />', $this->rootPath);
    print '<br />';

    return $this;
  }
}
