<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *

	You can use php's built in server
	php -S localhost:80 -c  c:\php\php.ini-development _mvp.php

	if you do, check first and exit if it's a public resource - Serve that instead
	 */
if (preg_match('/\.(?:png|ico|jpg|jpeg|gif|css|js)$/', $_SERVER['REQUEST_URI'])) {
  if (file_exists(trim($_SERVER['REQUEST_URI'], ' /\\')))
    return false;    // serve the requested resource as-is.

}

require __DIR__ . '/../../vendor/autoload.php'; // load the autoloader

application::run(); // run the application
