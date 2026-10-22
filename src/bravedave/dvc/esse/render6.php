<?php
/*
 * Copyright (c) 2025 David Bray
 * Licensed under the MIT License. See LICENSE file for details.
*/

namespace bravedave\dvc\esse;

use bravedave\dvc\{config, resources, strings};

use function bravedave\dvc\esc;

class render6 {

  public function __invoke(array $params = []): void {

    $options = array_merge([
      'title' => config::$WEBNAME,
      'navbar' => null,
      'aside' => null,
      'main' => fn() => null,
      'footer' => null,
      'breadcrumb' => [],
      'bodyClass' => '',
      'css' => [],
      'scripts' => [],
      'late' => [],
      'meta' => [],
    ], $params);

    $page = new page;
    $page->pageClass = 'render6';
    $page->bodyClass = esc($options['bodyClass']);
    $page->asideClass = 'render6-aside md:col-3 fg-secondary bg-1';
    $page->meta = ['<meta charset="utf-8">', '<meta name="viewport" content="width=device-width, initial-scale=1">'];

    foreach (['css', 'js'] as $type) {
      $hash = hash_file('sha384', resources::bootstrap6($type), true);
      $integrity = 'sha384-' . base64_encode($hash);
      $url = strings::url('assets/bootstrap/' . $type . '/6?v=' . bin2hex($hash));
      if ('css' == $type) {
        $page->css[] = sprintf('<link rel="stylesheet" href="%s" integrity="%s" crossorigin="anonymous">', esc($url), $integrity);
      } else {
        $imports = ['imports' => ['@bootstrap' => $url]];
        $page->scripts[] = '<script type="importmap">' . json_encode($imports, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES) . '</script>';
        $page->late[] = sprintf('<script type="module" src="%s" integrity="%s" crossorigin="anonymous"></script>', esc($url), $integrity);
      }
    }

    $page->css[] = sprintf('<link rel="stylesheet" href="%s">', esc(strings::url('assets/bootstrap/icons')));
    $page->css[] = sprintf('<link rel="stylesheet" href="%s">', esc(strings::url('assets/esse/6')));
    $page->scripts[] = sprintf('<script src="%s"></script>', esc(strings::url('assets/jquery/4')));

    foreach ($options['css'] as $css) {
      $page->css[] = preg_match('/^<(link|style)/', $css) ? $css : sprintf('<link rel="stylesheet" href="%s">', esc($css));
    }
    foreach ($options['scripts'] as $script) {
      $page->scripts[] = preg_match('/^<script/', $script) ? $script : sprintf('<script src="%s"></script>', esc($script));
    }
    $page->meta = array_merge($page->meta, $options['meta']);
    $page->late = array_merge($page->late, $options['late']);

    $page->head($options['title'])->body();
    if ($options['navbar']) $page->then($options['navbar']);
    if ($options['aside']) $page->aside()->then($options['aside']);
    $page->main();

    if ($options['breadcrumb']) {
      print '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
      foreach ($options['breadcrumb'] as $crumb) {
        if ($crumb->url ?? null) {
          printf('<li class="breadcrumb-item"><a href="%s">%s</a></li>', esc($crumb->url), esc($crumb->label));
        } else {
          printf('<li class="breadcrumb-item" aria-current="page">%s</li>', esc($crumb->label));
        }
      }
      print '</ol></nav>';
    }

    $page->then($options['main'])->footer();
    if ($options['footer']) $page->then($options['footer']);
    $page->close();
  }
}