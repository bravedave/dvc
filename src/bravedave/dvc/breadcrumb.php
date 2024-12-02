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

class breadcrumb {

  public string $label;
  public string $url;

  public function __construct(string $label, string $url = null) {

    $this->label = $label;
    if ( $url) $this->url = $url;
  }
}
