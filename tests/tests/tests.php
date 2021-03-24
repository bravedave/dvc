<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace tests;

use dvc;
use strings;

class tests extends dvc\service {
  public static function simple() {
    $app = new self( __DIR__);
    $app->_simple();

  }

  protected function _simple() {
    print strings::html2text('The Ultimate Cash Cow - 7% Return');
    print "\n";
    // print strings::xml_entities(strings::html2text('The Ultimate Cash Cow - 7% Return'));
    // print "\n";

  }

}
