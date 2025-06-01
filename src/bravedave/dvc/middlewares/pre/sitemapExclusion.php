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

trait sitemapExclusion {

  protected function preMiddleware(): array {

    $middleWares = [

      function (): bool {

        self::application()::app()->exclude_from_sitemap = true;
        return true;
      }
    ];

    return array_merge($middleWares, parent::preMiddleware());
  }
}
