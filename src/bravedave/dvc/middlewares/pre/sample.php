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

  protected function preMiddleware(): array {

    $middleWares = [

      function () {

        logger::info(sprintf('<sample middleware called ..> %s', logger::caller()));
        return true;
      }
    ];

    if ($parent = get_parent_class($this)) {

      logger::info( sprintf('<%s> %s', $parent, logger::caller()));

      // if the parent class has a preMiddleware method, call it
      $parentMiddlewares = method_exists($parent, 'preMiddleware')
        ? parent::preMiddleware()
        : [];

      return array_merge($middleWares, $parentMiddlewares);
    }

    return $middleWares;
  }
}
