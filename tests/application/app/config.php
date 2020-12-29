<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

class config extends dvc\config {}

if ( config::checkDBconfigured()) {
    config::$SITEMAPS = true;

}

config::route_register( 'halfmoon', '');
config::route_register( 'pageless');
config::route_register( 'tests', 'sample\controller');
config::route_register( 'webapp', 'webapp\controller');
config::route_register( 'bootstrap5', '');
config::route_register( 'home', '');
