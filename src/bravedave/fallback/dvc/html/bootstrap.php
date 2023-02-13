<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/

NameSpace dvc\html;
use \url;

class bootstrap extends page {
	function __construct() {
		parent::__construct();

		self::$indent = 0;
		$this->head->append( 'link', NULL, array( 'type' => 'text/css', 'rel' => 'stylesheet', 'media' => 'all', 'href' => url::$URL . 'css/bootstrap.min.css'));
		$this->head->append( 'link', NULL, array( 'type' => 'text/css', 'rel' => 'stylesheet', 'media' => 'all', 'href' => url::$URL . 'css/font-awesome.min.css'));
		$this->head->append( 'link', NULL, array( 'type' => 'text/css', 'rel' => 'stylesheet', 'media' => 'all', 'href' => url::$URL . 'css/dvc.css'));
		$this->head->append( 'script', NULL, array( 'type' => 'text/javascript', 'src' => url::$URL . 'js/jquery-2.1.1.min.js'));

		$this->children[] = new element( 'script', NULL, array( 'type' => 'text/javascript', 'src' => url::$URL . 'js/bootstrap.min.js'));

	}

}
