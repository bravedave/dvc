<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * using: https://www.scrapbook.cash/interfaces/key-value-store/
*/

namespace dvc;

use APCUIterator;

class cache {
  protected static $_instance;
  protected $_cache;
  protected $ttl = 60;

  protected function __construct() {
    if (\config::$DB_CACHE_DEBUG) \sys::logger(__METHOD__);

    $this->ttl = \config::$DB_CACHE_TTL;

    // create Scrapbook KeyValueStore object
    $this->_cache = new \MatthiasMullie\Scrapbook\Adapters\Apc;
  }

  static function instance() {
    if (!self::$_instance)
      self::$_instance = new cache;

    return (self::$_instance);
  }

  function get($key) {
    if ($res = $this->_cache->get($key)) {
      if (\config::$DB_CACHE_DEBUG) {
        \sys::logger(sprintf('get(%s) (hit) : %s', $key, __METHOD__));
      }
    } elseif (\config::$DB_CACHE_DEBUG) {
      \sys::logger(sprintf('get(%s) (miss) : %s', $key, __METHOD__));
    }

    return ($res);
  }

  function set($key, $value, $ttl = null) {

    if (!$ttl) $ttl = $this->ttl;

    if ($this->_cache->set($key, $value, $ttl)) {
      if (\config::$DB_CACHE_DEBUG) {
        \sys::logger(sprintf('set(%s) : %s', $key, __METHOD__));
      }
    }
  }

  function delete($key, $wildcard = false) {
    if ($wildcard) {
      if (\config::$DB_CACHE_DEBUG) {
        \sys::logger(sprintf('wildard delete(%s) : %s', $key, __METHOD__));
      }

      $cachedKeys = new APCUIterator($key);
      foreach ($cachedKeys as $_key) {
        if (\config::$DB_CACHE_DEBUG) {
          \sys::logger(sprintf('wildard delete(%s) => %s : %s', $key, $_key['key'], __METHOD__));
        }

        $this->_cache->delete($_key['key']);
      }
    } else {
      if (\config::$DB_CACHE_DEBUG) {
        \sys::logger(sprintf('delete(%s) : %s', $key, __METHOD__));
      }

      $this->_cache->delete($key);
    }
  }

  function flush() {
    if (\config::$DB_CACHE_DEBUG || \config::$DB_CACHE_DEBUG_FLUSH) {
      \sys::logger(sprintf('<flush> : %s', __METHOD__));
    }

    $this->_cache->flush();
  }
}
