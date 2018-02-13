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

		// create Scrapbook KeyValueStore object
		$this->_cache = new \MatthiasMullie\Scrapbook\Adapters\Apc;

		//~ // create stampede protector layer over our real cache
		//~ $this->_cache = new \MatthiasMullie\Scrapbook\Scale\StampedeProtector( $cache);

		//~ // create Pool (psr/cache) object from cache engine
		//~ $this->_pool = new \MatthiasMullie\Scrapbook\Psr6\Pool( $this->_cache);

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
		//~ // get item from Pool
		//~ $item = $this->_pool->getItem( $key);

		//~ // get item value
		//~ return ( $item->get());

	}

	function set( $key, $value) {
		if ( $this->_cache->set( $key, $value, $this->ttl)) {
			if ( \config::$DB_CACHE_DEBUG)
				\sys::logger( sprintf( 'dvc\cache : set(%s)', $key));

		}

		//~ // get item from Pool
		//~ $item = $this->_pool->getItem( $key);

		//~ // ... or change the value & store it to cache
		//~ $item->set( $value);

		//~ $this->_pool->save($item);

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
