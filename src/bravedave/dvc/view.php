<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\dvc;

use League\CommonMark\GithubFlavoredMarkdownConverter;

class view {
  public $data = null;
  public $loadName = '?';
  public $title = '';
  public $wrap = [];
  public $debug = false;

  protected $paths = [];
  protected $rootPath = false;

  function __construct($data = null) {

    if ($app = application::app()) {

      $this->paths[] = $this->rootPath = $app->getRootPath();
      foreach ($app->getPaths() as $path) {
        $this->paths[] = $path;
      }
    } else {

      $this->paths[] = $this->rootPath = __DIR__;
    }

    $this->data = $data;
  }

  static function instance() {

    return new static;
  }

  protected function _wrap() {

    if (count((array)$this->wrap)) {

      foreach ((array)$this->wrap as $wrap) {

        if ($wrap) printf('<div class="%s">', $wrap);
      }
    }

    return $this;  // chain
  }

  protected function _unwrap() {
    if (count((array)$this->wrap)) {
      foreach ((array)$this->wrap as $wrap) {
        if ($wrap) printf('</div><!-- wrap:div class="%s" -->', $wrap);
      }
    }

    return $this;  // chain
  }

  protected function _load($path) {

    if (substr_compare($path, '.md', -3) === 0) {

      if ($this->debug) logger::debug(sprintf('<it\'s an md !> %s', __METHOD__));

      $fc = file_get_contents($path);
      $converter = new GithubFlavoredMarkdownConverter([
        'html_input' => 'strip',
        'allow_unsafe_links' => false,
      ]);
      print $converter->convert($fc);
      // print \Parsedown::instance()->text($fc);
    } else {

      include $path;
    }

    return $this;  // chain
  }

  public function loadView($name) {

    return $this->load($name);
  }

  public function load($name) {

    $inPath = false;
    foreach ($this->paths as $path) {
      if ($path) {
        if (substr($name, 0, strlen($path)) === $path) {
          if (file_exists($name)) {
            $parts = pathinfo($name);
            $this->loadName = $parts['filename'];
            $path = $name;
            $inPath = true;
            if ($this->debug) logger::debug(sprintf('<%s> : found in path : %s', $name, __METHOD__));

            break;
          } elseif (file_exists($name . '.php')) {
            $name .= '.php';
            $parts = pathinfo($name);
            $this->loadName = $parts['filename'];
            $path = $name;
            $inPath = true;
            if ($this->debug) logger::debug(sprintf('<%s> : found in path : %s', $name, __METHOD__));

            break;
          }
        }
      }
    }

    if (!$inPath) {

      if ($this->debug) logger::debug(sprintf('<%s> : NOT in path (%s) : %s', $name, implode(', ', $this->paths), __METHOD__));
      $this->loadName = $name;
      $path = sprintf('%s/app/views/%s.php', $this->rootPath, $name);
    }

    if (file_exists($path)) {
      $this
        ->_wrap()
        ->_load($path)
        ->_unwrap();

      return true;
    } else {

      if ($this->debug) logger::debug(sprintf('<%s> : NOT found : %s', $name, __METHOD__));

      $path = sprintf('%s/views/%s.php', __DIR__, $name);
      if (file_exists($path)) {
        $this
          ->_wrap()
          ->_load($path)
          ->_unwrap();

        return (true);
      } else {

        // we are going to allow vendor/bravedave/dvc/theme
        if (class_exists('dvc\theme\view', /* autoload */ false)) {

          if ($themeView = '\dvc\theme\view'::getView($name)) {
            $this
              ->_wrap()
              ->_load($themeView)
              ->_unwrap();

            return true;
          }
        }

        printf('view::%s - not found<br />', $name);
        printf('root::%s<br />', $this->rootPath);
        print '<br />';
      }
    }

    return false;
  }
}
