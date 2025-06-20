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

  public function __construct(string $label, string|null $url = null) {

    $this->label = $label;
    if ( $url) $this->url = $url;
  }
}
