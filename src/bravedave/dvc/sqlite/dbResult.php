<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\dvc\sqlite;

use bravedave;
use SQLite3Result;

class dbResult {
  protected SQLite3Result|null $result;
  protected db|null $db;

  public function __construct(SQLite3Result|null $result = null, $db = null) {

    if ($result) $this->result = $result;
    if ($db) $this->db = $db;
  }

  public function __destruct() {
  }

  public function fetch() {
    
    return $this->result->fetchArray(SQLITE3_ASSOC);
  }

  public function dto($template = NULL) {

    if ($dto = $this->fetch()) {

      if (is_null($template)) return new bravedave\dvc\dto($dto);
      return new $template($dto);
    }

    return false;
  }

  /**
   *	extend like:
   *		$dtoSet = $res->dtoSet( fn( $dto) => return $dto);
   */
  public function dtoSet($func = null, $template = null) {
    $ret = [];
    if (is_callable($func)) {

      while ($dto = $this->dto($template)) {

        if ($d = $func($dto)) $ret[] = $d;
      }
    } else {

      while ($dto = $this->dto($template)) {

        $ret[] = $dto;
      }
    }

    return $ret;
  }
}
