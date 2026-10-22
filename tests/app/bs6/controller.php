<?php
/*
 * Copyright (c) 2026 David Bray
 * Licensed under the MIT License. See LICENSE file for details.
*/

namespace bs6;

use bravedave\dvc\controller as rootController;
use bravedave\dvc\esse\render6;
use bravedave\dvc\{breadcrumb, ServerRequest, strings};
use config;

class controller extends rootController {

  protected function _index() {

    $this->data = (object) [
      'asideId' => 'aside-' . strings::rand(),
      'title' => $title = $this->title = config::$WEBNAME
    ];

    $render = [
      'aside' => fn() => $this->load('aside6'),
      'footer' => fn() => $this->load('footer6'),
      'main' => fn() => $this->load('main6'),
      'navbar' => fn() => $this->load('navbar6'),
      'title' => $title,
      'breadcrumb' => [new breadcrumb('Bootstrap 6')],
    ];


    $renderer = new render6;
    $renderer($render);


  }

  protected function before() {
    parent::before();
    array_unshift($this->viewPath, __DIR__ . '/views/');
  }

  protected function postHandler() {
    $request = (new ServerRequest);
    $action = $request('action');
    return match ($action) {
      default => parent::postHandler()
    };
  }
}
