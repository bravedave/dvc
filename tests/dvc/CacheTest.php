<?php

namespace MatthiasMullie\Scrapbook\Adapters {

  class Apc {
    public static int $constructed = 0;
    public static int $operations = 0;
    private array $values = [];

    public function __construct() {
      self::$constructed++;
    }

    public function get(string $key): mixed {
      self::$operations++;
      return $this->values[$key] ?? false;
    }

    public function set(string $key, mixed $value, int $ttl): bool {
      self::$operations++;
      $this->values[$key] = $value;
      return true;
    }

    public function delete(string $key): void {
      self::$operations++;
      unset($this->values[$key]);
    }

    public function flush(): void {
      self::$operations++;
      $this->values = [];
    }
  }
}

namespace {

  use bravedave\dvc\cache;
  use MatthiasMullie\Scrapbook\Adapters\Apc;

  require __DIR__ . '/../../vendor/autoload.php';
  require_once __DIR__ . '/../app/config.php';
  require __DIR__ . '/../../src/bravedave/dvc/cache.php';

  final class CacheTest extends cache {
    private int $passed = 0;

    public function __construct() {}

    private function check(bool $condition, string $message): void {
      if (!$condition) throw new RuntimeException($message);
      $this->passed++;
    }

    public function run(): void {
      config::$DB_CACHE_DEBUG = false;
      config::$DB_CACHE_DEBUG_FLUSH = false;

      foreach (['', 'INVALID'] as $backend) {
        config::$DB_CACHE = $backend;
        self::$_instance = null;
        $cache = cache::instance();
        $this->check($cache instanceof cache, 'instance() must always return a cache object');
        $this->check($cache === cache::instance(), 'instance() must preserve the singleton');
        $this->check(!cache::enabled(), 'empty and unknown backends must be disabled');
        $this->check($cache->get('record') === false, 'disabled reads must miss');
        $cache->set('record', 'value');
        $cache->delete('record');
        $cache->delete('/record/', true);
        $cache->flush();
        $this->check(Apc::$constructed === 0, 'disabled construction must not create an APC adapter');
        $this->check(Apc::$operations === 0, 'disabled operations must not reach a backend');
      }

      config::$DB_CACHE = 'APC';
      self::$_instance = null;
      $cache = cache::instance();
      $this->check(cache::enabled(), 'explicit APC configuration must remain supported');
      $this->check(Apc::$constructed === 1, 'explicit APC configuration must construct its adapter');
      $cache->set('record', 'value');
      $this->check($cache->get('record') === 'value', 'enabled cache must read stored values');
      $cache->delete('record');
      $this->check($cache->get('record') === false, 'enabled deletion must reach the adapter');
      $cache->set('record', 'value');
      $cache->flush();
      $this->check($cache->get('record') === false, 'enabled flushing must reach the adapter');
      $cache->set('record', 'value');

      config::$DB_CACHE = '';
      $operations = Apc::$operations;
      $this->check($cache === cache::instance(), 'disablement must preserve the existing object');
      $this->check($cache->get('record') === false, 'retained objects must honor disabled reads');
      $cache->set('record', 'replacement');
      $cache->delete('record');
      $cache->delete('/record/', true);
      $cache->flush();
      $this->check(Apc::$operations === $operations, 'retained objects must not reach the disabled backend');

      printf("PASS: %d cache assertions (in-memory adapter only)\n", $this->passed);
    }
  }

  (new CacheTest)->run();
}