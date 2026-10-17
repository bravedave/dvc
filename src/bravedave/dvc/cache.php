<?php
/*
 * Copyright (c) 2026 David Bray
 * Licensed under the MIT License. See LICENSE file for details.
 *
 * using: https://www.scrapbook.cash/interfaces/key-value-store/
*/

namespace bravedave\dvc;

use APCUIterator, config, MatthiasMullie;
use Redis;

class cache {
  protected static null|cache $_instance = null;
  protected MatthiasMullie\Scrapbook\Adapters\Apc|MatthiasMullie\Scrapbook\Adapters\Redis|null $_cache;
  protected $ttl = 60;

  protected function __construct() {

    if (config::$DB_CACHE_DEBUG) logger::debug(__METHOD__);
    $this->ttl = config::$DB_CACHE_TTL;


    if (config::$DB_CACHE == 'REDIS') {

      // delta implemented: configurable Redis host or Unix socket path

      // create \Redis object pointing to your Redis server
      $client = new Redis;
      $client->connect(config::$DB_CACHE_REDIS_SERVER);
      // create Scrapbook KeyValueStore object
      $this->_cache = new MatthiasMullie\Scrapbook\Adapters\Redis($client);
    } else {

      // create Scrapbook KeyValueStore object
      $this->_cache = new MatthiasMullie\Scrapbook\Adapters\Apc;
    }
  }

  public static function enabled(): bool {

    if (config::$DB_CACHE == 'APC') return true;
    if (config::$DB_CACHE == 'REDIS') return true;

    return false;
  }

  public static function instance(): cache {

    if (!self::$_instance) self::$_instance = new cache;
    return (self::$_instance);
  }

  public function get(string $key) {

    if ($res = $this->_cache->get($key)) {

      if (config::$DB_CACHE_DEBUG) logger::debug(sprintf('get(%s) (hit) : %s', $key, __METHOD__));
    } elseif (config::$DB_CACHE_DEBUG) {

      logger::debug(sprintf('get(%s) (miss) : %s', $key, __METHOD__));
    }

    return $res;
  }

  public function set(string $key, mixed $value, $ttl = null) {

    if (!$ttl) $ttl = $this->ttl;

    if ($this->_cache->set($key, $value, $ttl)) {

      if (config::$DB_CACHE_DEBUG) logger::debug(sprintf('set(%s) : %s', $key, __METHOD__));
    }
  }

  public function delete(string $key, $wildcard = false) {
    if ($wildcard) {

      if (config::$DB_CACHE_DEBUG) logger::debug(sprintf('wildard delete(%s) : %s', $key, __METHOD__));

      $cachedKeys = new APCUIterator($key);
      foreach ($cachedKeys as $_key) {

        if (config::$DB_CACHE_DEBUG) logger::debug(sprintf('wildard delete(%s) => %s : %s', $key, $_key['key'], __METHOD__));
        $this->_cache->delete($_key['key']);
      }
    } else {

      if (config::$DB_CACHE_DEBUG) logger::debug(sprintf('delete(%s) : %s', $key, __METHOD__));
      $this->_cache->delete($key);
    }
  }

  public function flush() {

    if (config::$DB_CACHE_DEBUG || config::$DB_CACHE_DEBUG_FLUSH) logger::debug(sprintf('<flush> : %s', __METHOD__));
    $this->_cache->flush();
  }
}
