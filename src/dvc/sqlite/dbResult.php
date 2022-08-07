<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc\sqlite;

use dvc;

class dbResult {
  protected $result = false;
  protected $db;

  public function __construct($result = null, $db = null) {
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
      if (is_null($template)) {
        return new dvc\dao\dto\dto($dto);
      }

      return new $template($dto);
    }

    return false;
  }

  /**
   *	extend like:
   *		$dtoSet = $res->dtoSet( function( $dto) {
   *			return $dto;
   *
   *		});
   */
  public function dtoSet($func = null, $template = null) {
    $ret = [];
    if (is_callable($func)) {
      while ($dto = $this->dto($template)) {
        if ($d = $func($dto)) {
          $ret[] = $d;
        }
      }
    } else {
      while ($dto = $this->dto($template)) {
        $ret[] = $dto;
      }
    }

    return $ret;
  }
}
