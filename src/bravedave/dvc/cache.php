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
use Throwable;

class cache {
  protected static null|cache $_instance = null;
  protected MatthiasMullie\Scrapbook\Adapters\Apc|MatthiasMullie\Scrapbook\Adapters\Redis|null $_cache = null;
  protected ?Redis $_redis = null;
  protected $ttl = 60;
  protected ?string $generation = null;
  protected string $connectionServer = '';
  protected array $fills = [];
  protected static bool $storageWarning = false;

  protected function __construct() {

    if (config::$DB_CACHE_DEBUG) logger::debug(__METHOD__);
    $this->ttl = config::$DB_CACHE_TTL;


    if (config::$DB_CACHE == 'REDIS' && !config::$DB_CACHE_SEMAPHORE_PATH) {

      // delta implemented: configurable Redis host or Unix socket path

      try {

        // create \Redis object pointing to your Redis server
        $client = $this->_redis = new Redis;
        if (!$client->connect(config::$DB_CACHE_REDIS_SERVER)) {

          throw new RedisException('Unable to connect to Redis');
        }
        // create Scrapbook KeyValueStore object
        $this->_cache = new MatthiasMullie\Scrapbook\Adapters\Redis($client);
      } catch (Throwable $e) {

        logger::info(sprintf('<%s> %s', $e->getMessage(), __METHOD__));
        $this->_redis = null;
        $this->_cache = null;
      }
    } elseif (config::$DB_CACHE == 'APC') {

      // create Scrapbook KeyValueStore object
      $this->_cache = new MatthiasMullie\Scrapbook\Adapters\Apc;
    }
  }

  protected function clock(): int {
    return time();
  }

  protected function connectRedis(): void {
    if ($this->_cache !== null && $this->_redis !== null && $this->connectionServer === config::$DB_CACHE_REDIS_SERVER) return;
    $client = new Redis;
    if (!$client->connect(config::$DB_CACHE_REDIS_SERVER, 0, 1.0)) {
      throw new RedisException('Unable to connect to Redis');
    }
    $client->setOption(Redis::OPT_READ_TIMEOUT, 1.0);
    $this->_redis = $client;
    $this->_cache = new MatthiasMullie\Scrapbook\Adapters\Redis($client);
    $this->connectionServer = config::$DB_CACHE_REDIS_SERVER;
  }

  protected function probeRedis(): void {
    $this->connectRedis();
    if (!$this->_redis->ping()) throw new RedisException('Redis health probe failed');
  }

  protected function semaphoreFile(): string {
    $identity = [config::$DB_HOST, config::$DB_NAME, config::$DB_CACHE_PREFIX, config::$DB_CACHE_REDIS_SERVER];
    return rtrim(config::$DB_CACHE_SEMAPHORE_PATH, '/') . '/cache-health-' . hash('sha256', json_encode($identity)) . '.json';
  }

  protected function storageFailure(): void {
    if (!self::$storageWarning) {
      logger::info('Cache bypassed: shared health state is unavailable');
      self::$storageWarning = true;
    }
  }

  protected function saveState($file, array $state): bool {
    $json = json_encode($state, JSON_THROW_ON_ERROR);
    rewind($file);
    return ftruncate($file, 0) && fwrite($file, $json) === strlen($json) && fflush($file);
  }

  protected function guarded(callable $operation, mixed $fallback = null): mixed {
    if (!in_array(config::$DB_CACHE, ['APC', 'REDIS'], true)) return $fallback;
    if (config::$DB_CACHE !== 'REDIS' || !config::$DB_CACHE_SEMAPHORE_PATH) {
      return $this->_cache === null ? $fallback : $operation('');
    }

    $file = @fopen($this->semaphoreFile(), 'c+');
    if (!$file) {
      $this->storageFailure();
      return $fallback;
    }

    try {
      if (!flock($file, LOCK_EX)) {
        $this->storageFailure();
        return $fallback;
      }
      $contents = stream_get_contents($file);
      $now = $this->clock();
      $state = $contents === '' ? [
        'generation' => bin2hex(random_bytes(16)),
        'disabled' => false,
        'healthy_since' => null,
        'next_probe' => 0
      ] : json_decode($contents, true);
      if (!is_array($state) || !is_string($state['generation'] ?? null)
        || preg_match('/^[a-f0-9]{32}$/D', $state['generation']) !== 1
        || !is_bool($state['disabled'] ?? null) || !is_int($state['next_probe'] ?? null)
        || !array_key_exists('healthy_since', $state)
        || ($state['healthy_since'] !== null && !is_int($state['healthy_since']))) {
        $this->storageFailure();
        return $fallback;
      }

      $this->generation ??= $state['generation'];
      $changed = $contents === '';
      $recovered = false;
      try {
        if ($state['disabled']) {
          if ($now < $state['next_probe']) return $fallback;
          $this->probeRedis();
          $state['healthy_since'] ??= $now;
          $state['next_probe'] = $now + max(1, (int)config::$DB_CACHE_PROBE_INTERVAL);
          if ($now - $state['healthy_since'] < max(1, (int)config::$DB_CACHE_MAX_TTL)) {
            if (!$this->saveState($file, $state)) $this->storageFailure();
            return $fallback;
          }
          $state['disabled'] = false;
          $state['generation'] = bin2hex(random_bytes(16));
          $changed = $recovered = true;
        } else {
          $this->connectRedis();
        }
        if ($changed && !$this->saveState($file, $state)) {
          $this->storageFailure();
          return $fallback;
        }
        if ($recovered) logger::info('Cache health recovered after cooldown');
        $this->_redis->clearLastError();
        $result = $operation($state['generation']);
        if ($this->_redis->getLastError()) throw new RedisException('Redis operation failed');
        return $result;
      } catch (Throwable $error) {
        $wasDisabled = $state['disabled'];
        $state['disabled'] = true;
        $state['healthy_since'] = null;
        $state['next_probe'] = $now + max(1, (int)config::$DB_CACHE_PROBE_INTERVAL);
        $state['generation'] = bin2hex(random_bytes(16));
        $this->_cache = null;
        $this->_redis = null;
        if (!$this->saveState($file, $state)) $this->storageFailure();
        if (!$wasDisabled) logger::info('Cache disabled: Redis health or operation failure');
        return $fallback;
      }
    } finally {
      flock($file, LOCK_UN);
      fclose($file);
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
    if (config::$DB_CACHE == 'REDIS' && config::$DB_CACHE_SEMAPHORE_PATH) {
      return self::instance()->guarded(fn() => true, false);
    }
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

    if (!self::enabled()) return null;
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
    $this->fills[$key] = false;
    return $this->guarded(function (string $generation) use ($key) {
      $this->fills[$key] = $generation;
      $result = $this->_cache->get($this->backendKey($key, $generation));
      if (config::$DB_CACHE_DEBUG) logger::debug(sprintf('get(%s) (%s) : %s', $key, $result ? 'hit' : 'miss', __METHOD__));
      return $result;
    }, false);
  }

  public function set(string $key, mixed $value, $ttl = null) {
    $this->guarded(function (string $generation) use ($key, $value, $ttl) {
      if ($generation !== '' && ($this->fills[$key] ?? $this->generation) !== $generation) return;
      $expiry = (int)($ttl ?: $this->ttl);
      if ($generation !== '') {
        if ($expiry < 1) throw new RedisException('Shared cache requires a bounded TTL');
        $expiry = min($expiry, max(1, (int)config::$DB_CACHE_MAX_TTL));
      }
      $stored = $this->_cache->set($this->backendKey($key, $generation), $value, $expiry);
      if (!$stored && $generation !== '') {
        throw new RedisException('Redis cache write failed');
      }
      if ($stored && config::$DB_CACHE_DEBUG) logger::debug(sprintf('set(%s) : %s', $key, __METHOD__));
    });
  }

  public function delete(string $key, $wildcard = false) {
    $this->guarded(fn(string $generation) => $this->deleteBackend($key, $wildcard, $generation));
  }

  protected function backendKey(string $key, string $generation): string {
    return ($generation === '' ? '' : 'dvc-' . $generation . ':') . $key;
  }

  protected function deleteBackend(string $key, bool $wildcard, string $generation): void {
    if ($wildcard) {

      if (config::$DB_CACHE_DEBUG) logger::debug(sprintf('wildcard delete(%s) : %s', $key, __METHOD__));

      if ($this->_redis) {

        $prefix = $this->backendKey('', $generation);
        $match = $generation === '' ? '*' . trim($key, '/') . '*' : $prefix . '*';
        $cursor = null;
        do {

          $cachedKeys = $this->_redis->scan($cursor, $match, 1000);
          foreach ($cachedKeys ?: [] as $cachedKey) {

            if (preg_match($key, substr($cachedKey, strlen($prefix))) !== 1) continue;
            if (config::$DB_CACHE_DEBUG) logger::debug(sprintf('wildcard delete(%s) => %s : %s', $key, $cachedKey, __METHOD__));
            if ($generation !== '') {
              if ($this->_redis->del($cachedKey) === false) throw new RedisException('Redis cache invalidation failed');
            } else {
              $this->_cache->delete($cachedKey);
            }
          }
        } while ($cursor);

        return;
      }

      $cachedKeys = new APCUIterator($key);
      foreach ($cachedKeys as $_key) {

        if (config::$DB_CACHE_DEBUG) logger::debug(sprintf('wildcard delete(%s) => %s : %s', $key, $_key['key'], __METHOD__));
        $this->_cache->delete($_key['key']);
      }
    } else {

      if (config::$DB_CACHE_DEBUG) logger::debug(sprintf('delete(%s) : %s', $key, __METHOD__));
      if ($generation !== '') {
        if ($this->_redis->del($this->backendKey($key, $generation)) === false) throw new RedisException('Redis cache invalidation failed');
      } else {
        $this->_cache->delete($key);
      }
    }
  }

  public function flush() {
    $this->guarded(function (string $generation) {
      if (config::$DB_CACHE_DEBUG || config::$DB_CACHE_DEBUG_FLUSH) logger::debug(sprintf('<flush> : %s', __METHOD__));
      if ($generation !== '') {
        $this->deleteBackend('/.*/', true, $generation);
      } elseif ($this->_redis) {
        $this->_redis->flushDB();
      } else {
        $this->_cache->flush();
      }
    });
  }
}
