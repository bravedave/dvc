<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/
Namespace dvc;

class session {
	// THE only instance of the class
	protected static $instance;
	var $__session = array();
	var $open = FALSE;
	var $domain = NULL;

	protected function __construct() {
		$CookieParams = session_get_cookie_params();
		$CookieParams['path'] = '/';

		if ( !is_null( $this->domain))
			$CookieParams['domain'] = $this->domain;

		session_set_cookie_params(
			$CookieParams['lifetime'],
			$CookieParams['path'],
			$CookieParams['domain'],
			$CookieParams['secure'],
			$CookieParams['httponly']

		);

		session_cache_expire(30);
		session_start();

		$this->__session = $_SESSION;

		session_write_close();

	}

	protected function __destroy() {
		if ( $this->open)
			session_write_close();

	}

	protected function _edit() {
		if ( !$this->open) {
			session_cache_expire(30);
			session_start();
			$this->open = TRUE;

		}

	}

	protected function _get( $var, $default = '' ) {
		if ( isset( $this->__session[$var] ))
			return $this->__session[$var];

		return $default;

	}

	protected function _close() {
		if ( !isset( self::$instance ))
			self::$instance = new session();

		if ( $this->open) {
			$this->__session = $_SESSION;	// re-read session
			$this->open = FALSE;
			session_write_close();

		}

	}

	static function get( $var, $default = '' ) {
		if ( !isset( self::$instance ))
			self::$instance = new session();

		return ( self::$instance->_get( $var, $default));

	}

	static function set( $var, $val = NULL ) {
		self::edit();
		if ( is_null( $val )) {
			if ( isset( $_SESSION[ $var ]))
				unset( $_SESSION[ $var ]);

		}
		else
			$_SESSION[ $var ] = $val;


	}

	static function edit() {
		if ( !isset( self::$instance ))
			self::$instance = new session();

		self::$instance->_edit();

	}

	static function close() {
		if ( !isset( self::$instance ))
			self::$instance = new session();

		self::$instance->_close();

	}

	static function destroy() {
		self::close();

		session_start();
		session_destroy();

	}

	function domain( $domain = NULL ) {
		$ret =  $this->domain;
		if ( !is_null( $domain))
			$this->domain = $domain;

		return ( $ret);

	}

}
