<?php

namespace crond;

use bravedave\dvc\logger;
use Fiber;

class FibreModule2 {

  public function __invoke() {

    return new Fiber(function () {
      for ($j = 0; $j < 3; $j++) {
        logger::info(sprintf('<FibreModule2 pass %s> %s', $j, logger::caller()));
        Fiber::suspend();
      }
    });
  }
}
