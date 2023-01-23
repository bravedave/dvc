<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\dvc\html;

class h1 extends element {

  function __construct($content = null, $attributes = null) {

    parent::__construct('h1', $content, $attributes);
  }
}
