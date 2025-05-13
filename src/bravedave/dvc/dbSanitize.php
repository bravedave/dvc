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

final class dbSanitize {

  public mixed $value;
  public mixed $sanitized;

  public function __construct(mixed $v) {

    $this->value = $this->sanitized = $v;
    if (!config::$DB_SANITIZE) return;

    if (is_string($v)) {

      // if it starts with doctype, don't touch it
      if (preg_match('/^\s*<!DOCTYPE\s/i', $v)) return;

      // dont touch it if it is a base64 encoded image or file
      if (preg_match('/^data:(image|application)\/[^;]+;base64,/', $v)) {
        logger::info(sprintf('<not touching base64 data> %s', logger::caller()));
        return;
      }

      // Step 1: Trim input
      $v = trim($v);

      // Step 2: Decode HTML entities
      $v = html_entity_decode($v, ENT_QUOTES | ENT_HTML401, 'UTF-8');

      if ($v != $this->sanitized) {

        // logger::info(sprintf('trim & html_entity_decode: :%s: => :%s:', $this->sanitized, $v));
        $this->sanitized = $v;
      }
    }
  }

  public function __invoke(): mixed {

    return $this->sanitized;
  }
}
