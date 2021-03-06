<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc;

class session {
	// THE only instance of the class
	protected static $instance;
	var $__session = [];
	var $open = false;
	var $domain = null;

	protected function __construct() {
		$CookieParams = session_get_cookie_params();

		if ( !is_null( $this->domain))
		$CookieParams['domain'] = $this->domain;

		$CookieParams['secure'] = !(Request::get()->ServerIsLocal() || Request::get()->ClientIsLocal());

		if ( (float)phpversion() < 7.3) {
			$CookieParams['path'] = '/; samesite=lax';

			session_set_cookie_params(
				$CookieParams['lifetime'],
				$CookieParams['path'],
				$CookieParams['domain'],
				$CookieParams['secure'],
				$CookieParams['httponly']

			);

		}
		else {
			$CookieParams['path'] = '/';
			$CookieParams['samesite'] = 'lax';

			session_set_cookie_params( $CookieParams);

		}

		session_cache_expire(30);
		session_start();

		$this->__session = $_SESSION;

		session_write_close();

	}

	protected function __destroy() {
		if ( $this->open) {
			session_write_close();

		}

	}

	protected function _edit() {
		if ( !$this->open) {
			session_cache_expire(30);
			session_start();
			$this->open = true;

		}

	}

	protected function _get( $var, $default = '' ) {
		if ( isset( $this->__session[$var] )) {
			return $this->__session[$var];

		}

		return $default;

	}

	protected function _close() {
		if ( !isset( self::$instance )) {
			self::$instance = new session;

		}

		if ( $this->open) {
			$this->__session = $_SESSION;	// re-read session
			$this->open = false;
			session_write_close();

		}

	}

	static function get( $var, $default = '' ) {
		if ( !isset( self::$instance )) {
			self::$instance = new session;

		}

		return ( self::$instance->_get( $var, $default));

	}

	static function set( $var, $val = null ) {
		self::edit();
		if ( is_null( $val )) {
			if ( isset( $_SESSION[ $var ]))
				unset( $_SESSION[ $var ]);

		}
		else
			$_SESSION[ $var ] = $val;


	}

	static function edit() {
		if ( !isset( self::$instance )) {
			self::$instance = new session;

		}

		self::$instance->_edit();

	}

	static function close() {
		if ( !isset( self::$instance )) {
			self::$instance = new session;

		}

		self::$instance->_close();

	}

	static function destroy() {
		self::close();

		session_start();
		session_destroy();

	}

	function domain( $domain = null ) {
		$ret =  $this->domain;
		if ( !is_null( $domain)) {
			$this->domain = $domain;

		}

		return ( $ret);

	}

}
