<?php

namespace crond;

use Fiber;

class FibreModule2 {

  public $name = 'FibreModule2';
  protected $iteration = 0;

  public function __invoke() {

    $o = new Fiber(function () {
      
      for ($j = 0; $j < 3; $j++) {

        $this->iteration++;

        if (php_sapi_name() === 'cli') {
          echo sprintf('<%s pass %s>', $this->name, $this->iteration) . PHP_EOL;
        } else {
          syslog(LOG_INFO, sprintf('<%s pass %s>', $this->name, $this->iteration));
        }
        Fiber::suspend();
      }
    });

    $this->name = 'harry';

    return $o;
  }
}
