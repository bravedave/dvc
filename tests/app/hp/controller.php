<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace hp;

use bravedave\dvc\{controller as dvcController, Response, ServerRequest};

class controller extends dvcController {

  protected function _index() {

    // 'pageUrl' => strings::url($this->route),
    // 'searchFocus' => true,
    $this->data = (object)[
      'title' => $this->title = config::label,
    ];

    $this->renderBS5([
      'aside' => false,
      'navbar' => fn() => $this->load('navbar'),
      'main' => fn() => $this->load('home')
    ]);
  }

  protected function before() {
    parent::before();
    $this->viewPath[] = __DIR__ . '/views/';
  }

  // protected function postHandler() {
  //   $request = new ServerRequest;
  //   $action = $request('action');
  //   return match ($action) {
  //     default => parent::postHandler()
  //   };
  // }

  public function images($img = '') {

    $f = match ($img) {
      'thoughtful.png' => Response::serve(__DIR__ . '/images/thoughtful.png'),
      'workspace.jpg' => Response::serve(__DIR__ . '/images/workspace.jpg'),
      default => Response::serve(__DIR__ . '/images/blank.svg')
    };
  }
}
