<?php
/*
	BrayWorth Pty Ltd
	PO Box 292, Tugun Q. Australia 4224
	Tel/Fax: +61 7 55982108

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	if you run php as a server, check first and exit if it's an image
	 */
if (preg_match('/\.(?:png|ico|jpg|jpeg|gif|css|js)$/', $_SERVER['REQUEST_URI'])) {
	if ( file_exists( $_SERVER['REQUEST_URI']))
		return false;    // serve the requested resource as-is.

	/*
	 * the file may be a public resource - Server that instead
	 */

}

// load the autoloader
require __DIR__ . '/../application/autoloader.php';

// run the application
application::run();
