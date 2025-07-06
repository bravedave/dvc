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

use config, Closure, sys;
use mysqli_stmt, SQLite3Stmt;

class dtoSet {

  protected sqlite\db|db $db;

  function __construct(sqlite\db|db|null $db = null) {

    if (!config::checkDBconfigured()) {

      // logger::info( sprintf('<Call the doctor I think I\'m gonna crash> %s', __METHOD__));
      // logger::info( sprintf('<The doctor say he\'s coming but you gotta create a config file buddy> %s', __METHOD__));
      throw new Exceptions\DBNotConfigured;
    }

    $this->db = is_null($db) ? sys::dbi() : $db;
  }

  public function __invoke(string|SQLite3Stmt|mysqli_stmt $sql, Closure|null $func = null, string|null $template = null): array {

    return $this->getDtoSet($sql, $func, $template);
  }

  public function getDtoSet(string|SQLite3Stmt|mysqli_stmt $sql, Closure|null $func = null, string|null $template = null): array {

    $res = null;

    if ($sql instanceof mysqli_stmt) {
      
      // if $sql is a mysqli_stmt, we can use it directly
      $res = new dbResult($sql->get_result(), $this->db);
    } elseif ($sql instanceof SQLite3Stmt) {

      // if $sql is a prepared statement, we can use it directly
      $res = new sqlite\dbResult($sql->execute(), $this->db);
    } else {

      // otherwise, we assume it's a query string
      $res = $this->db->result($sql);
    }

    return $res ? $res->dtoSet($func, $template) : [];
  }

  public function quote(string $s): string {

    return $this->db->quote($s);
  }
}
