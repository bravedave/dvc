<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc;

abstract class menu {
  static public function build($a) {

    $s = '<ul class="menu">';
    foreach ($a as $item)
      $s .= $item->resolve() . PHP_EOL;

    $s .= '</ul>';

    return ($s);
  }

  static public function MainContextMenu() {
  }
}
