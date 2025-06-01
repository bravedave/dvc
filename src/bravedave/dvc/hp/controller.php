<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\dvc\hp;

use bravedave\dvc\{controller as dvcController, Response};

class controller extends dvcController {

  protected function _index() {

    // 'pageUrl' => strings::url($this->route),
    // 'searchFocus' => true,
    $this->data = (object)[
      'aside' => false,
      'title' => $this->title = config::label,
    ];

    $this->renderBS5([
      'aside' => false,
      'main' => fn() => $this->load('home')
    ]);
  }

  protected function preMiddleware(): array {

    $middleWares = [function (): bool {

      $this->viewPath[] = __DIR__ . '/views/';
      return true;
    }];

    return array_merge($middleWares, parent::preMiddleware());
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
