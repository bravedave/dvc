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

config::route_register( 'tests', 'sample\controller');
config::route_register( 'home', '');
