<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	Credits:
		* A simple PHP MVC skeleton :: https://github.com/panique/php-mvc/

	You can use php's built in server
	php -S localhost:80 -c  c:\php\php.ini-development  index.php

	if you do, check first and exit if it's a public resource - Serve that instead
	 */
if (preg_match('/\.(?:png|ico|jpg|jpeg|gif|css|js)$/', $_SERVER['REQUEST_URI'])) {
	if ( file_exists( $_SERVER['REQUEST_URI']))
		return false;    // serve the requested resource as-is.

}

// load the autoloader
require __DIR__ . '/../application/autoloader.php';

// run the application
application::run();
