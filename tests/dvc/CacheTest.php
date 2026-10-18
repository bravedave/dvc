<?php

namespace MatthiasMullie\Scrapbook\Adapters {

  class Redis {
    public function __construct($client) {}
    public function get(string $key): mixed {
      if (\Redis::$failure === 'get') throw new \RuntimeException('Simulated read failure');
      return \Redis::$values[$key] ?? false;
    }
    public function set(string $key, mixed $value, int $ttl): bool {
      if (\Redis::$failure === 'set') return false;
      \Redis::$values[$key] = $value;
      \Redis::$lastTTL = $ttl;
      return true;
    }
  }

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

  if (!class_exists('Redis') && ($argv[1] ?? '') !== '--missing-extension-worker') {
    class RedisException extends RuntimeException {}
    class Redis {
      const OPT_READ_TIMEOUT = 3;
      public static array $values = [];
      public static string $failure = '';
      public static int $lastTTL = 0;
      public static int $connections = 0;
      public static int $flushes = 0;
      public function connect($host, $port = 6379, $timeout = 0): bool {
        self::$connections++;
        return self::$failure !== 'connect';
      }
      public function setOption($option, $value): void {}
      public function ping(): bool { return self::$failure !== 'probe'; }
      public function clearLastError(): void {}
      public function getLastError(): ?string { return self::$failure === 'scan' ? 'Simulated scan failure' : null; }
      public function del(string $key): int|false {
        if (self::$failure === 'delete') return false;
        $found = isset(self::$values[$key]);
        unset(self::$values[$key]);
        return (int)$found;
      }
      public function scan(&$cursor, string $pattern, int $count): array|false {
        $cursor = 0;
        if (self::$failure === 'scan') return false;
        $prefix = rtrim($pattern, '*');
        return array_values(array_filter(array_keys(self::$values), fn($key) => str_starts_with($key, $prefix)));
      }
      public function flushDB(): void { self::$flushes++; }
    }
  }

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

  final class CacheSemaphoreTest extends cache {
    private int $passed = 0;
    private static int $now = 100;

    public function __construct() { parent::__construct(); }

    protected function clock(): int { return self::$now; }

    private function check(bool $condition, string $message): void {
      if (!$condition) throw new RuntimeException($message);
      $this->passed++;
    }

    private static function configure(string $directory): void {
      config::$DB_CACHE = 'REDIS';
      config::$DB_HOST = 'test-host';
      config::$DB_NAME = 'live';
      config::$DB_CACHE_PREFIX = 'test-prefix';
      config::$DB_CACHE_REDIS_SERVER = 'test-redis';
      config::$DB_CACHE_SEMAPHORE_PATH = $directory;
      config::$DB_CACHE_MAX_TTL = 10;
      config::$DB_CACHE_PROBE_INTERVAL = 2;
      config::$DB_CACHE_TTL = 5;
      self::$_instance = new self;
    }

    public static function worker(string $directory, int $now): void {
      self::configure($directory);
      self::$now = $now;
      exit(cache::enabled() ? 1 : 0);
    }

    public function run(): void {
      $directory = sys_get_temp_dir() . '/dvc-cache-test-' . bin2hex(random_bytes(8));
      mkdir($directory);
      try {
        self::configure($directory);
        $cache = cache::instance();
        $this->check(cache::enabled(), 'Healthy Redis must enable caching');
        $cache->set('record', 'before', 1000);
        $this->check(Redis::$lastTTL === 10, 'Shared entries must have a bounded maximum TTL');
        $this->check($cache->get('record') === 'before', 'Guarded Redis must serve enabled hits');
        $cache->get('pending-fill');
        $oldKeys = array_keys(Redis::$values);

        Redis::$failure = 'get';
        $this->check($cache->get('record') === false, 'Read failures must fall back to SQL');
        $connections = Redis::$connections;
        Redis::$failure = '';
        $this->check(!cache::enabled(), 'All callers must remain disabled before the next probe');
        $this->check(Redis::$connections === $connections, 'Disabled checks must rate-limit probes');
        $paused = new self;
        $this->check($paused->get('paused-fill') === false, 'An instance created during an outage must bypass reads');
        $cache->set('record', 'must-not-store');
        $cache->delete('record');
        $cache->flush();
        $this->check(array_keys(Redis::$values) === $oldKeys, 'Retained instances must not mutate a disabled backend');

        $pipes = [];
        $process = proc_open([PHP_BINARY, '-n', __FILE__, '--disabled-worker', $directory, '100'], [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
        if (!is_resource($process)) throw new RuntimeException('Unable to launch isolated child');
        $output = stream_get_contents($pipes[1]) . stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $this->check(proc_close($process) === 0, 'A separate process must observe shared disablement: ' . $output);

        self::$now = 102;
        $this->check(!cache::enabled(), 'First healthy probe must start cooldown, not enable caching');
        self::$now = 110;
        $this->check(!cache::enabled(), 'Cooldown must last the maximum TTL');
        Redis::$failure = 'probe';
        self::$now = 112;
        $this->check(!cache::enabled(), 'Renewed failure must reset recovery');
        Redis::$failure = '';
        self::$now = 114;
        $this->check(!cache::enabled(), 'Recovery must restart after renewed failure');
        self::$now = 124;
        $this->check(cache::enabled(), 'Successful probe after full cooldown must re-enable caching');
        $cache->set('pending-fill', 'stale');
        $paused->set('paused-fill', 'stale');
        $this->check(count(Redis::$values) === count($oldKeys), 'Pre-outage fills must be rejected after recovery');
        $this->check($cache->get('record') === false, 'Recovery must not reuse old-generation entries');
        $cache->set('record', 'fresh');
        $this->check($cache->get('record') === 'fresh', 'Fresh fills must work after recovery');

        config::$DB_NAME = 'snapshot';
        self::$_instance = new self;
        $other = cache::instance();
        $this->check($other->get('record') === false, 'Datasets sharing Redis must use different namespaces');
        $other->set('record', 'snapshot');
        config::$DB_NAME = 'live';
        $this->check($cache->get('record') === 'fresh', 'Other dataset writes must not affect live data');
        $cache->flush();
        config::$DB_NAME = 'snapshot';
        $this->check($other->get('record') === 'snapshot' && Redis::$flushes === 0, 'Guarded flush must preserve other datasets and never flushDB');
        $other->delete('/^record$/', true);
        $this->check($other->get('record') === false, 'Wildcard invalidation must match original keys');
        $other->set('record', 'snapshot');
        Redis::$failure = 'delete';
        $other->delete('record');
        $this->check(!cache::enabled(), 'Invalidation failure must trip the shared semaphore');
        Redis::$failure = '';

        config::$DB_CACHE = '';
        self::$now = 200;
        $this->check(!cache::enabled() && $other->get('record') === false, 'Permanent disablement must override recovery');
        config::$DB_CACHE = 'REDIS';
        config::$DB_CACHE_SEMAPHORE_PATH = $directory . '/missing';
        $this->check(!cache::enabled(), 'Unavailable shared storage must fail closed');
        config::$DB_CACHE_SEMAPHORE_PATH = $directory;
        file_put_contents($other->semaphoreFile(), '{invalid');
        $this->check(!cache::enabled(), 'Corrupt shared state must fail closed');

        config::$DB_NAME = 'connection-failure';
        self::$_instance = new self;
        Redis::$failure = 'connect';
        $this->check(!cache::enabled(), 'Connection failures must disable caching without throwing');
        Redis::$failure = '';
        config::$DB_NAME = 'write-failure';
        self::$_instance = new self;
        $writer = cache::instance();
        Redis::$failure = 'set';
        $writer->set('record', 'value');
        $this->check(!cache::enabled(), 'False write results must trip the semaphore');
        Redis::$failure = '';

        config::$DB_NAME = 'scan-failure';
        self::$_instance = new self;
        $scanner = cache::instance();
        $scanner->set('record', 'value');
        Redis::$failure = 'scan';
        $scanner->delete('/record/', true);
        $this->check(!cache::enabled(), 'Failed wildcard scans must trip the semaphore');
        Redis::$failure = '';

        config::$DB_NAME = 'live';
        $process = proc_open([PHP_BINARY, '-n', __FILE__, '--missing-extension-worker', $directory, '200'], [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
        if (!is_resource($process)) throw new RuntimeException('Unable to launch missing-extension child');
        $output = stream_get_contents($pipes[1]) . stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $this->check(proc_close($process) === 0, 'Missing extension must fail closed in another process: ' . $output);
        self::$_instance = $cache;
        $this->check(!cache::enabled(), 'Missing-extension failure must disable other processes sharing the identity');
        printf("PASS: %d semaphore assertions (isolated files/processes and in-memory Redis only)\n", $this->passed);
      } finally {
        Redis::$failure = '';
        config::$DB_CACHE = '';
        config::$DB_CACHE_SEMAPHORE_PATH = '';
        self::$_instance = null;
        foreach (glob($directory . '/*') as $file) unlink($file);
        rmdir($directory);
      }
    }
  }

  if (in_array($argv[1] ?? '', ['--disabled-worker', '--missing-extension-worker'], true)) CacheSemaphoreTest::worker($argv[2], (int)$argv[3]);
  (new CacheTest)->run();
  if (!extension_loaded('redis')) {
    (new CacheSemaphoreTest)->run();
  } else {
    echo "Run php -n tests/dvc/CacheTest.php for isolated Redis semaphore coverage.\n";
  }
}