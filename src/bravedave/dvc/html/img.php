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

class img extends element {
  function __construct($src = '', $alt = null, $attributes = []) {

    $_attributes = ['src' => $src];

    if (!is_null($alt)) {
      $_attributes['alt'] = (string)$alt;
    }

    if ((array)$attributes) {
      $_attributes = array_merge($_attributes, $attributes);
    }

    parent::__construct('img', null, $_attributes);
  }
}
