<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * DO NOT change this file
 * Copy it to <application>/app/dvc/ and modify it there
*/

namespace bravedave\dvc;

class timer {
  public $start;

  function __construct() {

    $this->start = $this->getmicrotime();
  }

  protected function getmicrotime(): float {

    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
  }

  public function elapsed(): string {

    $end = $this->getmicrotime();
    $time = round($end - $this->start, 2);
    return "$time s";
  }
}
