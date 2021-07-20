<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

class config extends dvc\config {
  // const lockdown = true;
  // const use_inline_logon = true;

  static public function initialize() {
    parent::initialize();

    config::route_register('beds', 'beds_list\controller');
    config::route_register('tests', 'sample\controller');
    config::route_register('webapp', 'webapp\controller');

    // not using these tests - unregister if registered
    config::route_register('halfmoon', '');
    config::route_register('pageless', '');
    config::route_register('bootstrap5', '');
    config::route_register('home', '');
  }
}
