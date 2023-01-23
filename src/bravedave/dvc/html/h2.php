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

class h2 extends element {

  function __construct($content = null, $attributes = null) {

    parent::__construct('h2', $content, $attributes);
  }
}
