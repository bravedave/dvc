<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	using:
		https://www.scrapbook.cash/interfaces/key-value-store/

	*/

Namespace dvc;

class cache {
	protected static $_instance;
	protected $_cache;
	protected $ttl = 60;

	protected function __construct() {
		if ( \config::$DB_CACHE_DEBUG)
			\sys::logger( 'dvc\cache : __construct');

		$this->ttl = \config::$DB_CACHE_TTL;

		// create Scrapbook KeyValueStore object
		$this->_cache = new \MatthiasMullie\Scrapbook\Adapters\Apc;

	}

	static function instance() {
		if ( !self::$_instance)
			self::$_instance = new cache;

		return ( self::$_instance);

	}

	function get( $key) {

		if ( $res = $this->_cache->get( $key)) {
			if ( \config::$DB_CACHE_DEBUG)
				\sys::logger( sprintf( 'dvc\cache : get(%s) (hit)', $key));

		}
		elseif ( \config::$DB_CACHE_DEBUG) {
			\sys::logger( sprintf( 'dvc\cache : get(%s) (miss)', $key));

		}

		return ( $res);

	}

	function set( $key, $value) {
		if ( $this->_cache->set( $key, $value, $this->ttl)) {
			if ( \config::$DB_CACHE_DEBUG)
				\sys::logger( sprintf( 'dvc\cache : set(%s)', $key));

		}

	}

	function delete( $key) {
		if ( \config::$DB_CACHE_DEBUG)
			\sys::logger( sprintf( 'dvc\cache : delete(%s)', $key));

		$this->_cache->delete( $key);

	}

	function flush() {
		if ( \config::$DB_CACHE_DEBUG)
			\sys::logger( 'dvc\cache : flush');

		$this->_cache->flush();

	}

}
