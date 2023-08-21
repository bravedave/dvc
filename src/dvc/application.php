<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 *  DO NOT change this file
 *  Copy it to <application>/app/dvc/ and modify it there
*/

namespace dvc;

use bravedave;

class application extends bravedave\dvc\application {

  protected function deprecated() {

    bravedave\dvc\logger::deprecated(sprintf('%s called in %s', __CLASS__, static::class));
  }
}
