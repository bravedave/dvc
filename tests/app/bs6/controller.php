<?php
/*
 * Copyright (c) 2026 David Bray
 * Licensed under the MIT License. See LICENSE file for details.
*/

namespace bs6;

use bravedave\dvc\controller as rootController;
use bravedave\dvc\esse\render6;
use bravedave\dvc\ServerRequest;
use config;

class controller extends rootController {

  protected function _index() {

    $data = (object) [
      'title' => config::$WEBNAME
    ];

    $render = new render6($data);
    $render($render->demo());


    // $this->data = (object)[
    //  'aside' => config::index_set,
    //  'help' => cms\docs\config::help__blank_page,
    //  'pageUrl' => strings::url($this->route),
    //  'searchFocus' => true,
    //  'title' => $this->title = ,
    // ];

    // $this->renderBS5([
    //  'main' => fn () => $this->load('blank')
    // ]);
  }

  protected function before() {
    parent::before();
    $this->viewPath[] = __DIR__ . '/views/';
  }

  protected function postHandler() {
    $request = (new ServerRequest);
    $action = $request('action');
    return match ($action) {
      default => parent::postHandler()
    };
  }
}
