<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

// #[AllowDynamicProperties]
namespace dvc\dao\dto;

class _dto {

  public $id = 0;

  public function __construct($row = null) {
    $this->populate($row);
  }

  protected function populate($row = null) {
    if (!(is_null($row))) {
      foreach ($row as $k => $v) {
        $this->{$k} = $v;
      }
    }
  }

  public function toString() {
    $s = array();
    foreach ($this as $k => $v) {
      $s[] = sprintf('%s = %s', $k, $v);
    }

    return (implode(PHP_EOL, $s));
  }
}
