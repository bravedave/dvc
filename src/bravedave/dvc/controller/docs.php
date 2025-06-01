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

use bravedave\dvc\{json, logger, Response};
use config, Controller, sys;
use strings;

class docs extends Controller {
  protected $RequireValidation = config::lockdown;
  protected $enable_editing = false;

  protected function _hasImage($img = '', ?string $controller = null) {

    $debug = false;
    // $debug = true;

    if (is_null($controller)) $controller = $this->name;

    $_paths = $this->_getViewPaths($controller);
    foreach ($_paths as $_path) {

      if ($debug) logger::info(sprintf('<%s> %s', $_path, __METHOD__));

      if (file_exists($__f = implode(DIRECTORY_SEPARATOR, [$_path, $img]))) {

        return $__f;
      }
    }

    $_paths = $this->_getSystemViewPaths($controller);
    foreach ($_paths as $_path) {

      if (file_exists($__f = implode(DIRECTORY_SEPARATOR, [$_path, $img]))) {

        return $__f;
      }
    }

    return false;
  }

  protected function _index(string $view = 'index.md', string|null $fileName = null) {

    // $this->debug = true;

    if (!$view) $view = 'index.md';
    if ($this->debug) logger::debug(sprintf('<%s> %s', $view, __METHOD__));

    if (!$fileName) $fileName = $view;

    if (preg_match('@\.(png|jpg|svg)$@', $view) && $_img = $this->_hasImage($view)) {

      Response::serve($_img);
    } else {

      $contents = ['contents'];
      if ($this->hasView($_c = sprintf('%s-contents', $fileName))) {

        $contents = [$_c];
      } else if (strpos($fileName, '-') !== false) {

        if ($this->hasView($_c = sprintf('%s-contents', preg_replace('/-.*/', '', $fileName)))) {
          $contents = [$_c];
        }
      }

      $primary = [(string)$view];
      if ('icons' == $view) {
        $primary[] = 'icons-code';
        $primary[] = 'icons-credit';
      }
      $primary[] = 'docs-format';

      /**
       * editing is not enabled by default
       * to enable editing, set $this->enable_editing = true;
       * and provide a postHandler that can save the file
       */
      if ($this->enable_editing) {

        $primary[] = 'docs-edit';
        $this->viewPath[] = $p = implode(DIRECTORY_SEPARATOR, [config::$SYSTEM_VIEWS, 'docs']);
        $this->_viewPathsVerified = []; // reset
      }

      $this->data = (object)[
        'title' => $this->title = sprintf('Docs - %s', ucwords($view)),
        'pageUrl' => strings::url(rtrim($this->route, '/') . '/' . ($view == 'index.md' ? '' : $view)),
        'searchFocus' => true,
        'file' => (string)$fileName,
        'new' => $this->enable_editing
          ? 'new-file' == $view
          : false,
      ];

      $primary[] = 'mermaid';

      $render = [
        'meta' => [
          sprintf('<base href="%s" />', strings::url('docs/'))
        ],
        'main' => fn() => array_walk($primary, fn($_) => $this->load($_, null, ['html_input' => 'allow'])),
        'aside' => fn() => array_walk($contents, fn($_) => $this->load($_, null, ['html_input' => 'allow'])),
      ];

      if (config::$DOCS_SYNTAX_HIGHLIGHT) {

        // '<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/10.1.1/styles/default.min.css">'
        $render['css'] = [
          '<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/styles/github-gist.min.css">'
        ];

        $render['scripts'] = [
          '<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/highlight.min.js"></script>',
          '<script>hljs.highlightAll();</script>'
        ];
      }

      $this->renderBS5($render);
    }
  }

  protected function getDoc() {

    $action = $this->getPost('action');
    if ($doc = $this->getPost('file')) {

      if ('new-file' == $doc) {

        $this->viewPath[] = $p = implode(DIRECTORY_SEPARATOR, [config::$SYSTEM_VIEWS, 'docs']);
        $this->_viewPathsVerified = []; // reset
      }

      if ($file = $this->_getView($doc)) {

        if (file_exists($file)) {

          return json::ack($action)
            ->data(file_get_contents($file));
        }
      }
    }

    return json::nak($action);
  }

  protected function postHandler() {
    $action = $this->getPost('action');

    return match ($action) {
      '-get-doc-' => $this->getDoc(),
      default => parent::postHandler()
    };
  }

  protected function load($viewName = 'index', $controller = null, array $options = []) {

    if (!isset($options['renderer'])) $options['renderer'] = [];
    if (!isset($options['renderer']['soft_break'])) $options['renderer']['soft_break'] = '<br>';

    return parent::load($viewName, $controller, $options);
  }
}
