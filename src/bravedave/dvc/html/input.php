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

class input extends element {

  function __construct($type = 'text', $attributes = NULL) {

    parent::__construct('input', NULL, $attributes);

    $this->attributes(array('type' => $type));
    $this->selfClosing = TRUE;
  }
}
