<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\dvc\controller;

use config, Controller, sys;
use strings;

class docs extends Controller {
  protected $RequireValidation = config::lockdown;

  protected function _hasImage($img = '', $controller = null) {

    if (is_null($controller)) $controller = $this->name;

    $_paths = $this->_getViewPaths($controller);
    foreach ($_paths as $_path) {
      if (file_exists($__f = implode(DIRECTORY_SEPARATOR, [$_path, $img]))) {
        return $__f;
      }
    }

    $_paths = [
      implode(DIRECTORY_SEPARATOR, [self::application()::app()->getInstallPath(), 'dvc', 'views', $controller]),
      implode(DIRECTORY_SEPARATOR, [self::application()::app()->getInstallPath(), 'dvc', 'views']),

    ];

    foreach ($_paths as $_path) {
      if (file_exists($__f = implode(DIRECTORY_SEPARATOR, [$_path, $img]))) {
        return $__f;
      }
    }

    return false;
  }

  protected function _index($view = 'index.md') {

    // $this->debug = true;

    if (!$view) $view = 'index.md';
    if ($this->debug) sys::logger(sprintf('<%s> %s', $view, __METHOD__));

    if (preg_match('@\.(png|jpg)$@', $view) && $_img = $this->_hasImage($view)) {

      sys::serve($_img);
    } else {

      $contents = ['contents'];
      if ($this->hasView($_c = sprintf('%s-contents', $view))) {

        $contents = $_c;
      } else if (strpos($view, '-') !== false) {

        if ($this->hasView($_c = sprintf('%s-contents', preg_replace('/-.*/', '', $view)))) {
          $contents = $_c;
        }
      }

      $primary = [(string)$view];
      if ('icons' == $view) {
        $primary[] = 'icons-code';
        $primary[] = 'icons-credit';
      }
      $primary[] = 'docs-format';

      $render = [
        'title' => $this->title = sprintf('Docs - %s', ucwords($view)),
        'primary' => $primary,
        'secondary' => $contents,
        'data' => (object)[
          'searchFocus' => false,
          'pageUrl' => rtrim( strings::url($this->route), '/') . '/' . $view == 'index.md' ? '' : $view
        ]
      ];

      if (config::$SYNTAX_HIGHLIGHT_DOCS) {

        // '<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/10.1.1/styles/default.min.css">'
        $render['css'] = [
          '<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/10.1.1/styles/github-gist.min.css">'

        ];

        $render['scripts'] = [
          '<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/10.1.1/highlight.min.js"></script>',
          '<script>hljs.initHighlightingOnLoad();</script>'
        ];
      }

      $this->render($render);
    }
  }

  public function index($view = 'index') {
    $this->isPost() ?
      $this->postHandler() :
      $this->_index($view);
  }
}
