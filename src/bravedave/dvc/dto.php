<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\dvc;

use Closure;
use stdClass;

class dto extends stdClass {

  public $id = 0;

  public function __construct($row = null) {

    $this->populate($row);
  }

  /**
   * returns the first row of a query as a dto
   */
  public function __invoke(int|string $sql, Closure $func = null, string $template = null): ?self {

    if (is_string($sql)) {

      if (is_null($template)) $template = $this::class;
      if ($dtoSet = (new dtoSet)($sql, $func, $template)) {

        if ($dto = array_shift($dtoSet)) return $dto;
      }
    }

    return null;
  }

  protected function populate($row = null) {

    // logger::info(sprintf('<allowing dynamic properties> %s', __METHOD__));
    if (!(is_null($row))) {

      foreach ($row as $k => $v) {

        $this->{$k} = $v;
      }
    }
  }

  public function toString() {

    $s = [];
    foreach ($this as $k => $v) {

      $s[] = sprintf('%s = %s', $k, $v);
    }

    return implode(PHP_EOL, $s);
  }
}
