<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 * 
 * MIT License
 *
*/

namespace crond;

use Fiber;

class FibreModule1 {

  public function __invoke() {
    return new Fiber(function () {
      for ($j = 0; $j < 3; $j++) {
        if (php_sapi_name() === 'cli') {
          echo sprintf('<FibreModule1 pass %s>', $j) . PHP_EOL;
        } else {
          syslog(LOG_INFO, sprintf('<FibreModule1 pass %s>', $j));
        }
        Fiber::suspend();
      }
    });
  }
}
