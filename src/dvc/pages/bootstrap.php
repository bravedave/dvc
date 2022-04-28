<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc\pages;

use config;
use currentUser;
use strings, dvc\Exceptions\InvalidBootstrapVersion;

class bootstrap extends page {

  static $SCALE = 1;

  static $contentClass = 'col pt-3 pb-4';
  static $primaryClass = 'col pt-3 pb-4 d-print-w100';
  static $secondaryClass = 'col-md-3 pt-3 pb-4 d-print-none';

  function __construct($title = '') {

    parent::$pageContainer = self::$pageContainer;
    self::$BootStrap = true;
    if (self::$Bootstrap_Version == '3') {
      self::$primaryClass = 'col-md-9 pt-3 pb-4 d-print-w100';
      $this->jQuery2 = true;
    } elseif ($this->dvc) {
      if (self::$Bootstrap_Version == '4') {
        $this->dvc = '4';
      } elseif (self::$Bootstrap_Version == '5') {
        $this->dvc = '4';
      }
    }

    parent::__construct($title);
    if (!self::$pageContainer) {
      self::$pageContainer = 'container-fluid pb-2';
    }

    $this->meta[] = sprintf('<meta name="viewport" content="width=device-width, initial-scale=%s, shrink-to-fit=no" />', self::$SCALE);

    if (self::$Bootstrap_Version == '3') {
      $css = strings::url('bootstrap.3/css/bootstrap.min.css');
      $js = strings::url('bootstrap.3/js/bootstrap.min.js');

      array_unshift($this->css, sprintf('<link type="text/css" rel="stylesheet" media="all" href="%s" />', $css));

      $this->latescripts[] = sprintf('<script type="text/javascript" src="%s"></script>', $js);
    } elseif (self::$Bootstrap_Version == '4') {
      $css = strings::url('assets/bootstrap/css');
      if ($theme = currentUser::option('theme')) {
        $css = strings::url('assets/bootstrap/css?t=' . $theme);
      } elseif ($theme = config::$THEME) {
        $css = strings::url('assets/bootstrap/css?t=' . $theme);
      }
      $icons = strings::url('assets/bootstrap/icons');
      $js = strings::url('assets/bootstrap/js');

      array_unshift($this->css, sprintf('<link type="text/css" rel="stylesheet" media="all" href="%s" />', $icons));
      array_unshift($this->css, sprintf('<link type="text/css" rel="stylesheet" media="all" href="%s" />', $css));

      $this->latescripts[] = sprintf('<script type="text/javascript" src="%s"></script>', $js);
    } elseif (self::$Bootstrap_Version == '5') {
      $css = strings::url('assets/bootstrap/css/5');
      if ($theme = config::$THEME) {
        $css = strings::url('assets/bootstrap/css/5?t=' . $theme);
      }
      $polyfill = strings::url('assets/bootstrap/polyfill/5');
      $icons = strings::url('assets/bootstrap/icons');
      $js = strings::url('assets/bootstrap/js/5');

      array_unshift($this->css, sprintf('<link type="text/css" rel="stylesheet" media="all" href="%s" />', $icons));
      array_unshift($this->css, sprintf('<link type="text/css" rel="stylesheet" media="all" href="%s" />', $polyfill));
      array_unshift($this->css, sprintf('<link type="text/css" rel="stylesheet" media="all" href="%s" />', $css));

      $this->latescripts[] = sprintf('<script type="text/javascript" src="%s"></script>', $js);
    } else {
      throw new InvalidBootstrapVersion;
    }
  }

  public function content($class = null, $more = null) {
    if (is_null($class))
      $class = self::$contentClass;

    return (parent::content($class, $more));  // chain

  }

  public function primary($class = null, $more = null, $tag = null) {
    return (parent::primary($class ?? self::$primaryClass, $more, $tag));  // chain

  }

  public function secondary($class = null, $more = null, $tag = null) {
    return (parent::secondary($class ?? self::$secondaryClass, $more, $tag));  // chain

  }
}
