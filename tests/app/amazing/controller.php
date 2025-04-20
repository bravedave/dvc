<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 * 
 * MIT License
 *
*/

namespace amazing;

use bravedave\dvc\{Response, ServerRequest, strings};
use bravedave;
use config;
use DirectoryIterator;

class controller extends bravedave\dvc\controller {

  protected function _index() {

    $this->data = (object)[
      'title' => $this->title = config::$WEBNAME,
    ];

    $this->renderBS5([
      'css' => [sprintf('<link rel="shortcut icon" href="%s" />', strings::url('favicon.ico'))],
      'aside' => fn() => $this->load('blank'),
      'main' => fn() => $this->load('main')
    ]);
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

  public function js(string $module = '') {

    /**
     * there are some modules in the module directory
     * get a list of them
     *
     * if $module matches one of them, serve it
     * use a iterator to do this
     */
    if ($module = preg_replace('/[^a-zA-Z0-9\_\-]/', '', $module)) {

      $iterator = new DirectoryIterator(__DIR__ . '/modules/');
      foreach ($iterator as $fileinfo) {

        if ($fileinfo->isDir()) continue;

        if (0 === strpos($fileinfo->getFilename(), $module)) {

          Response::serve($fileinfo->getPathname());
          return;
        }
      }
    }

    // otherwise fall through

    return parent::js($module);
  }
}
