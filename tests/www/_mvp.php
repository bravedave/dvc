<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *

	You can use php's built in server
	php -S localhost:8080 -c  c:\php\php.ini-development _mvp.php

	if you do, check first and exit if it's a public resource - Serve that instead
	 */
if (preg_match('/\.(?:png|ico|jpg|jpeg|gif|css|js|txt)$/', $_SERVER['REQUEST_URI'])) {

	if (file_exists(trim($_SERVER['REQUEST_URI'], ' /\\'))) return false;    // serve the requested resource as-is.
}

// load the autoloader
if (file_exists($autoload = __DIR__ . '/../../vendor/autoload.php')) {

	require $autoload;
} elseif (file_exists($autoload = __DIR__ . '/../vendor/autoload.php')) {

	require $autoload;
}
application::run(); // run the application
