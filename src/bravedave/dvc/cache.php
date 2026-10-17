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
use RedisException;

class cache {
  protected static null|cache $_instance = null;
  protected MatthiasMullie\Scrapbook\Adapters\Apc|MatthiasMullie\Scrapbook\Adapters\Redis|null $_cache;
  protected ?Redis $_redis = null;
  protected $ttl = 60;

  protected function __construct() {

    if (config::$DB_CACHE_DEBUG) logger::debug(__METHOD__);
    $this->ttl = config::$DB_CACHE_TTL;


    if (config::$DB_CACHE == 'REDIS') {

      // delta implemented: configurable Redis host or Unix socket path

      // create \Redis object pointing to your Redis server
      $client = $this->_redis = new Redis;
      $client->connect(config::$DB_CACHE_REDIS_SERVER);
      // create Scrapbook KeyValueStore object
      $this->_cache = new MatthiasMullie\Scrapbook\Adapters\Redis($client);
    } else {

      // create Scrapbook KeyValueStore object
      $this->_cache = new MatthiasMullie\Scrapbook\Adapters\Apc;
    }
  }

  protected static function apcstats(): ?array {

    if (function_exists('apcu_cache_info')) {

      $cache = apcu_cache_info($limited = true);
      // logger::info(sprintf('<%s> %s', print_r($cache, true), __METHOD__));

      $sma = apcu_sma_info($limited = true);
      $time = time();
      $req_rate = 0;
      $hit_rate = 0;
      $miss_rate = 0;

      if ($time - $cache['start_time']) {

        $req_rate = sprintf(
          "%.2f",
          ($cache['num_hits'] + $cache['num_misses']) ? (($cache['num_hits'] + $cache['num_misses']) / ($time - $cache['start_time'])) : 0
        );

        $hit_rate = sprintf("%.2f", $cache['num_hits'] ? (($cache['num_hits']) / ($time - $cache['start_time'])) : 0);
        $miss_rate = sprintf("%.2f", $cache['num_misses'] ? (($cache['num_misses']) / ($time - $cache['start_time'])) : 0);
      }

      $start_time = $cache['start_time'];
      if ($start_time < strtotime('2000-01-01')) {


        $_cache = self::instance();
        $start_time = $_cache->get('cache_start_time');

        if (!$start_time) {

          $start_time = time();
          $_cache->set('cache_start_time', $start_time, 86400);

          logger::info(sprintf('<bad cache start time %s> %s', date('c', $start_time), __METHOD__));
        }
      }

      return [
        'hit' => $cache['num_hits'],
        'miss' => $cache['num_misses'],
        'req_rate' => $req_rate,
        'hit_rate' => $hit_rate,
        'miss_rate' => $miss_rate,
        'mem_used' => $cache['mem_size'],
        'mem_avail' => $sma['avail_mem'],
        'up' => $time - $start_time
      ];
    }

    return null;
  }

  /**
   * redis cache statistics, same shape as apcstats()
   *
   * @return ?array
   */
  protected static function redisstats(): ?array {

    if (!class_exists('Redis')) return null;

    if ('REDIS' != config::$DB_CACHE) return null;
    $host = config::$DB_CACHE_REDIS_SERVER;

    try {

      // same call as bravedave\dvc\cache - accepts a hostname or a socket path
      $redis = new Redis;
      $redis->connect($host);

      $info = $redis->info();
      $redis->close();
    } catch (RedisException $e) {

      logger::info(sprintf('<%s> %s', $e->getMessage(), __METHOD__));
      return null;
    }

    $hits = (int)($info['keyspace_hits'] ?? 0);
    $misses = (int)($info['keyspace_misses'] ?? 0);
    $up = (int)($info['uptime_in_seconds'] ?? 0);
    $mem_used = (int)($info['used_memory'] ?? 0);
    $max_mem = (int)($info['maxmemory'] ?? 0);

    // maxmemory 0 means unlimited; fall back to system memory
    if (!$max_mem) $max_mem = (int)($info['total_system_memory'] ?? 0);

    return [
      'hit' => $hits,
      'miss' => $misses,
      'req_rate' => sprintf('%.2f', $up ? ($hits + $misses) / $up : 0),
      'hit_rate' => sprintf('%.2f', $up ? $hits / $up : 0),
      'miss_rate' => sprintf('%.2f', $up ? $misses / $up : 0),
      'mem_used' => $mem_used,
      'mem_avail' => max(0, $max_mem - $mem_used),
      'up' => $up
    ];
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

  /**
   * cache statistics
   *
   * @return ?array
   */
  public static function stats(): ?array {

    // test what cache system is available and return appropriate stats
    if ('APC' == config::$DB_CACHE) return self::apcstats();
    if ('REDIS' == config::$DB_CACHE) return self::redisstats();
    return null;
  }

  public static function type(): string {

    if ('APC' == config::$DB_CACHE) return 'APC';
    if ('REDIS' == config::$DB_CACHE) return 'REDIS';
    return '';
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

      if (config::$DB_CACHE_DEBUG) logger::debug(sprintf('wildcard delete(%s) : %s', $key, __METHOD__));

      if ($this->_redis) {

        $cursor = null;
        do {

          $cachedKeys = $this->_redis->scan($cursor);
          foreach ($cachedKeys ?: [] as $cachedKey) {

            if (preg_match($key, $cachedKey) !== 1) continue;
            if (config::$DB_CACHE_DEBUG) logger::debug(sprintf('wildcard delete(%s) => %s : %s', $key, $cachedKey, __METHOD__));
            $this->_cache->delete($cachedKey);
          }
        } while ($cursor !== 0);

        return;
      }

      $cachedKeys = new APCUIterator($key);
      foreach ($cachedKeys as $_key) {

        if (config::$DB_CACHE_DEBUG) logger::debug(sprintf('wildcard delete(%s) => %s : %s', $key, $_key['key'], __METHOD__));
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
