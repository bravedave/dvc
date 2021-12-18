<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * DO NOT change this file
 * Copy it to <application>/app/dvc/ and modify it there
 *
*/

namespace dvc\pages;

use dvc\core\bs;

class bootstrap5 extends bootstrap {

  function __construct($title = '') {
    bs::$VERSION = \config::$BOOTSTRAP_VERSION = '5';
    self::$Bootstrap_Version = '5';

    parent::__construct($title);

    $this->topOfPage = [];
  }

  public function primary($class = null, $more = null, $tag = 'main') {
    return (parent::primary($class, $more, $tag));  // chain

  }

  public function secondary($class = null, $more = null, $tag = 'aside') {
    return (parent::secondary($class, $more, $tag));  // chain

  }
}
