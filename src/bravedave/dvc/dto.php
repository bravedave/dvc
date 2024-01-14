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

#[AllowDynamicProperties]
class dto {

  public $id = 0;

  public function __construct($row = null) {
    $this->populate($row);
  }

  protected function populate($row = null) {

    // logger::info(sprintf('<allowing dynamic properties> %s', __METHOD__));
    if (!(is_null($row))) {

      foreach ($row as $k => $v) {

        $this->{$k} = $v;
      }
    }
  }

  public function toString() {

    $s = [];
    foreach ($this as $k => $v) {

      $s[] = sprintf('%s = %s', $k, $v);
    }

    return implode(PHP_EOL, $s);
  }
}
