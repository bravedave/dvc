<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

class NavMenuItem extends dvc\menuitem {

  function __construct($label, $url = false, $style = '') {

    parent::__construct($label, $url, $style);
    $this->className = '';
  }
}
