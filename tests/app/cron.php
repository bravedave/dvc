<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * DVC Cron System Example Implementation
 * Usage:
 *   - Define your fibre modules (implementing __invoke() and returning a Fiber) 
 *     anywhere in your codebase.
 *   - Register fibre modules for the cron service in your config class via the static crons() function.
 *     Example:
 *       static function crons(): array {
 *         return array_merge(parent::crons(), [
 *           MyNamespace\FibreModule1::class,
 *           MyNamespace\FibreModule2::class
 *         ]);
 *       }
 *   - The maximum number of cron cycles is set by config::$CROND_MAX_RUNS. 
 *     If set to zero, the cron will run indefinitely.
 *   - This script is designed to be called from system cron (e.g., every minute).
 *   - Prevents overlapping runs using a semaphore file (cron.semaphore).
 *   - If the semaphore is older than 10 minutes, it is cleaned up automatically.
 *   - Supports graceful exit via an exit semaphore (cron.exit).
 *   - Each run cycles through all registered fibres, resuming each and updating the semaphore.
 *   - Errors in individual fibres are logged, but do not stop other fibres or the main loop.
 *   - On exit, the semaphore is removed and all fibres are given a chance to exit cleanly.
 *
 * Example system cron entry:
 *   * * * * * php /path/to/this/cron.php
 *     
 * Example fibre module:
 *   class FibreModule1 {
 *     public function __invoke() {
 *       return new Fiber(function () {
 *         for ($j = 0; $j < 3; $j++) {
 *           syslog(LOG_INFO, sprintf('<FibreModule1 pass %s>', $j));
 *           Fiber::suspend();
 *         }
 *       });
 *     }
 *   }
*/

require dirname(dirname(__DIR__)) . '/vendor/autoload.php';

class cron extends bravedave\dvc\service {

  static function crond() {
    $app = new self(application::startDir());
    $app->_execute(fn() => bravedave\dvc\crond::cron());
  }
}

if (php_sapi_name() == 'cli' && basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
  cron::crond();
}
