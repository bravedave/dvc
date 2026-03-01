<?php
/*
 * Copyright (c) 2025 David Bray
 * Licensed under the MIT License. See LICENSE file for details.
*/

namespace bravedave\dvc\controller;

use config, Controller, sys;

class home extends Controller {
  protected $RequireValidation = config::lockdown;

  protected function postHandler() {
    $action = $this->getPost('action');
    if ($action == 'get-template') {

      $template = $this->getPost('template');
      sys::getTemplate($template);
    } else {

      parent::postHandler();
    }
  }

  protected function _index($data = '') {
    // just points into the documentation

    $readme = implode(DIRECTORY_SEPARATOR, [
      dirname(dirname(dirname(dirname(__DIR__)))),
      'README.md'
    ]);

    $primary = [$readme];
    $secondary = ['docs/contents'];
    $sample = implode(DIRECTORY_SEPARATOR, [$this->rootPath, 'controller', 'hello.php']);
    if (file_exists($sample)) {
      $primary[] = 'docs/sample';
      $secondary[] = 'docs/sample-index';
    }

    // $this->render([
    //   'title' => $this->title,
    //   'primary' => $primary,
    //   'secondary' => $secondary
    // ]);

    $this->data = (object)[
      'title' => $this->title = config::$WEBNAME,
    ];

    $this->renderBS5([
      'aside' => false,
      'main' => fn() => array_walk($primary, fn($_) => $this->load($_))
    ]);
  }

  public function index($data = '') {

    /*
			if you set this you will get some stats in the system log
			about how many loads have occurrered	*/
    parent::index($data);
  }
}
