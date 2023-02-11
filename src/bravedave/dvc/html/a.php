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

class a extends element {

  function __construct($href = '', $content = null, $attributes = null) {
    if (is_null($content))
      $content = $href;

    parent::__construct('a', $content, ['href' => $href]);

    if (!is_null($attributes)) $this->attributes($attributes);
  }
}
