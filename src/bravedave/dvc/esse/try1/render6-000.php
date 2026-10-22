<?php
/*
 * Copyright (c) 2026 David Bray
 * Licensed under the MIT License. See LICENSE file for details.
 *
 * Opt-in Bootstrap 6 page shell for controller-owned rendering.
 *
 * Designed to be used from a controller, the renderer does not
 * *load* for instance - it provides slots for rendering and
 * assumes a Closure manages data handling.
 *
 * Usage:
 *  $renderer = new render6;
 *  $renderer([
 *    'main' => fn() => $this->load('main')
 *  ]);
 *
 *
 * Review findings (2026-10-23, supersedes try1 for this class):
 * - Verdict: fit for the narrower page-shell role as an opt-in foundation;
 *   not a drop-in renderBS5 replacement or a complete Bootstrap 6 workbench.
 * - Controller-owned closures preserve view context and data handling. View
 *   discovery, Markdown conversion and data extraction are deliberately out
 *   of scope, not missing renderer features. page owns document closure.
 *
 * Findings, highest impact first:
 * - Legacy JavaScript is still unconditional: jQuery, _brayworth_ and dopo
 *   load on every page. Existing modal/toast helpers call jQuery plugins;
 *   the Bootstrap 6 ES module does not establish that legacy contract.
 *   Remove or explicitly opt into this stack when adopting native modules;
 *   dopo also requires _brayworth_ and cannot be retained independently.
 * - Mobile aside content is hidden below md, with no alternate access supplied
 *   by this shell. Acceptable for optional secondary content; required actions
 *   or navigation need an accessible alternative supplied by the caller.
 * - Asset options are trusted developer input: raw tags are emitted verbatim
 *   and URL shorthand is interpolated without attribute escaping. Do not pass
 *   request/user values through these options; escape URL attributes if that
 *   contract is broadened. Breadcrumb/title text is already escaped, but
 *   breadcrumb URL schemes are not validated.
 *
 * Growth boundaries:
 * - @bootstrap is a working ES-module import, not a global bootstrap object.
 *   scripts URL shorthand creates classic scripts; module callers must supply
 *   explicit type="module" tags. late entries must be complete markup.
 * - Future helpers can be imported per view without bringing view loading or
 *   application logic back into the renderer. Add import-map composition only
 *   when needed, rather than recreating the entire _brayworth_ global API.
 * - Bootstrap version globals/controller data are unchanged. Legacy helpers
 *   are not automatically switched to 6; keep that compatibility decision
 *   outside the current shell contract.
 * - page still uses application aliases, theme and currentUser. This is a
 *   DVC-integrated renderer, not a framework-independent HTML renderer.
 *
 * Validation:
 * - /bs6 browser smoke check passed: complete document, @bootstrap exports,
 *   desktop/mobile overflow checks and print overflow/aside styles; no page
 *   errors or failed requests. Mobile aside is hidden as specified above.
 * - Static demo coverage does not verify modal/tab/toast workflows, long
 *   content, oversized navbar/footer slots or physical print pagination.
 *   Focused committed renderer regression tests remain outstanding.
*/

namespace bravedave\dvc\esse;

use bravedave\dvc\breadcrumb;
use bravedave\dvc\config;
use bravedave\dvc\resources;
use bravedave\dvc\strings;

use function bravedave\dvc\esc;

class render6 {

  public function __construct() {
  }

  public function __invoke(array $params = []) {

    $options = array_merge([
      'title' => config::$WEBNAME,
      'main' => fn() => null,
      'breadcrumb' => [],
      'aside' => null,
      'footer' => null,
      'navbar' => null,
      'css' => [],
      'scripts' => [],
      'late' => [],
      'meta' => [],
    ], $params);

    $crumbs = fn() => null;
    if ($options['breadcrumb']) {

      $crumbs = function () use ($options) {

        print '<nav aria-label="breadcrumb"><ol class="breadcrumb">';

        array_walk($options['breadcrumb'], function (breadcrumb $crumb) {

          if ($crumb->url ?? null) {

            printf(
              '<li class="breadcrumb-item"><a href="%s">%s</a></li>',
              esc($crumb->url),
              esc($crumb->label)
            );
          } else {

            printf('<li class="breadcrumb-item">%s</li>', esc($crumb->label));
          }
        });

        print '</ol></nav>';
      };
    }

    $page = new page;
    $page->asideClass = 'd-none md:d-block md:col-3 py-3 pb-4 fg-secondary bg-1';
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

    $imports = (object)[
      'imports' => (object)[]
    ];
    $imports->imports->{'@bootstrap'} = $js;
    $page->scripts[] = sprintf('<script type="importmap">%s</script>', json_encode($imports, JSON_UNESCAPED_SLASHES));

    array_walk($options['css'], fn($_) => $page->css[] = preg_match('/^<(link|style)/', $_) ? $_ : sprintf('<link rel="stylesheet" href="%s">', $_));
    array_walk($options['scripts'], fn($_) => $page->scripts[] = preg_match('/^<script/', $_) ? $_ : sprintf('<script src="%s"></script>', $_));

    array_walk($options['late'], fn($_) => $page->late[] = $_);
    array_walk($options['meta'], fn($_) => $page->meta[] = $_);

    $page
      ->head($options['title'])
      ->body();

    if ($options['navbar']) $page->then($options['navbar']);

    if ($options['aside']) $page->aside()->then($options['aside']);
    $page
      ->main()
      ->then($crumbs)
      ->then($options['main'])
      ->footer();
    if ($options['footer']) $page->then($options['footer']);
  }
}
