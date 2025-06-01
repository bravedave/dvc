<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\dvc\middlewares\pre;

use bravedave\dvc\logger;

trait sample {

  protected function getMiddleware(): array {

    $middleWares = [

      function () {

        logger::info(sprintf('<sample middleware called ..> %s', logger::caller()));
        return true;
      }
    ];

    return array_merge($middleWares, parent::getMiddleware());
  }
}
