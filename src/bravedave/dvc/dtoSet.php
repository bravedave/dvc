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

use config, Closure;

class dtoSet {

  protected sqlite\db|db $db;

  function __construct(sqlite\db|db $db = null) {

    if (!config::checkDBconfigured()) {

      // logger::info( sprintf('<Call the doctor I think I\'m gonna crash> %s', __METHOD__));
      // logger::info( sprintf('<The doctor say he\'s coming but you gotta create a config file buddy> %s', __METHOD__));
      throw new Exceptions\DBNotConfigured();
    }

    $this->db = is_null($db) ? \sys::dbi() : $db;
  }

  public function __invoke(string $sql, Closure $func = null, string $template = null): array {

    return $this->getDtoSet($sql, $func, $template);
  }

  public function getDtoSet(string $sql, Closure $func = null, string $template = null): array {

    if ($res = $this->db->result($sql)) return $res->dtoSet($func, $template);
    return [];
  }

  public function quote($s) {

    return $this->db->quote($s);
  }
}
