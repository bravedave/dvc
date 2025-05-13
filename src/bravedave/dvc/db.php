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

use config, Exception;

class db {
  protected $mysqli, $dbname;

  protected static $_cachePrefix = null;

  public $log = false;

  public static function cachePrefix(): string {

    if (config::$DB_CACHE_PREFIX) {

      return config::$DB_CACHE_PREFIX;
    } elseif ('mysql' == config::$DB_TYPE) {

      return str_replace('.', '_', config::$DB_HOST . '_' . config::$DB_NAME);
    } else {

      /**
       * it's probably sqlite, so we need a unique prefix for this database
       *
       * this could require further development if we are going to support
       * multiple cached sqlite databases in the same application, otherwise
       * this database, this appication is unique
       * */

      return config::getDBCachePrefix();
    }
  }

  public static function dbTimeStamp(): string | bool {

    return date("Y-m-d H:i:s", time());
  }

  public static function mysqli_field_type($type_id) {
    static $types;

    if (!isset($types)) {
      $types = array();
      $constants = get_defined_constants(true);
      foreach ($constants['mysqli'] as $c => $n) if (preg_match('/^MYSQLI_TYPE_(.*)/', $c, $m)) $types[$n] = $m[1];
    }

    return array_key_exists($type_id, $types) ? $types[$type_id] : "unKnown";
  }

  function __construct($host, $database, $user, $pass) {
    $this->dbname = $database;
    $this->mysqli = @new \mysqli($host, $user, $pass, $database);

    if ($this->mysqli->connect_error) {

      logger::info(sprintf('\mysqli( %s, %s, ***, %s )',  $host, $user, $database));
      logger::info(sprintf('Connect Error (%s) %s', $this->mysqli->connect_errno, $this->mysqli->connect_error));
      throw new Exceptions\UnableToSelectDatabase;
    }

    // $this->mysqli->set_charset('utf8');
    $this->mysqli->set_charset('utf8mb4');
  }

  function __destruct() {
    if ($this->mysqli) {

      if ($a = $this->mysqli->error_list) {

        array_walk($a, fn($e) => logger::info(sprintf('<mysql-error : %s> %s', $e, __METHOD__)));
      }
      $this->mysqli->close();
      $this->mysqli = null;
    }
  }

  function __invoke(string $query): ?dbResult {
    return $this->result($query);
  }

  public function affected_rows() {
    return ($this->mysqli->affected_rows);
  }

  public function dump() {
    if ($dbR = $this->result(sprintf('SHOW TABLES FROM %s', config::$DB_NAME))) {
      $uID = 0;
      while ($row = $dbR->fetch_row()) {
        printf(
          '<span data-role="visibility-toggle" data-target="bqt%s">Table: %s</span><br />%s',
          $uID,
          $row[0],
          PHP_EOL
        );
        printf(
          '<blockquote id="bqt%s" style="font-family: monospace; display: none;">%s',
          $uID++,
          PHP_EOL
        );

        /* Get field information for all columns */
        if ($res = $this->result(sprintf('SELECT * FROM `%s` LIMIT 1', $this->escape($row[0])))) {
          $finfo = $res->fetch_fields();

          foreach ($finfo as $val)
            printf('<br />%s %s (%s)', $val->name, $this->field_type($val->type), $val->length);
        }

        print "</blockquote>\n";
      }
    } else {
      printf(
        '<pre>
				DB Error, could not list tables
				MySQL Error: %s
				MySQL Host: %s
			</pre>',
        mysqli_error($this->mysqli),
        config::$DB_HOST
      );
    }
  }

  public function escape($s): string {
    return $this->mysqli->real_escape_string($s);
  }

  public function fetchFields($table) {
    $res = $this->Q("SELECT * FROM `$table` LIMIT 1");
    return ($res->fetch_fields());
  }

  public function flushCache() {

    if (config::$DB_CACHE == 'APC') {
      /**
       * the automatic caching is controlled by:
       *	=> \dao\_dao->getByID addes to cache
       *  => \dao\_dao->UpdateByID flushes the cache selectively
       *		 - and sets flushCache to FALSE - so you won't be here
       *
       *	if you are here it is because Update was called casually outside
       *	of UpdateByID <=> a master flush is required
       */
      $cache = cache::instance();
      $cache->flush();
      if (config::$DB_CACHE_DEBUG || config::$DB_CACHE_DEBUG_FLUSH) {

        foreach (debug_backtrace() as $e) {

          logger::info(sprintf('post flush: %s(%s)', $e['file'], $e['line']));
        }
      }
    }
  }

  public function field_exists($table, $field) {
    $ret = FALSE;

    $result = $this->Q("SHOW COLUMNS FROM $table");
    if (mysqli_num_rows($result) > 0) {
      while ($row = mysqli_fetch_assoc($result)) {
        if ($row['Field'] == $field) {
          $ret = TRUE;
          break;
        }
      }
    }
    return ($ret);
  }

  public function field_type($v) {
    return (self::mysqli_field_type($v));
  }

  public function fieldList($table) {
    $result = $this->Q("SHOW COLUMNS FROM `$table`");
    $ret = array();
    while ($row = mysqli_fetch_assoc($result))
      $ret[] = $row["Field"];

    return ($ret);
  }

  public function getCharSet() {
    return ($this->mysqli->character_set_name());
  }

  public function getDBName(): string {

    return (string)$this->dbname;
  }

  public function Insert($table, $a) {
    $fA = [];
    $fV = [];
    foreach ($a as $k => $v) {
      
      $fA[] = $k;
      // $fV[] = $this->quote($v);
      $fV[] = $this->quote((new dbSanitize($v))());
    }

    $sql = sprintf(
      'INSERT INTO `%s`(`%s`) VALUES(%s)',
      $table,
      implode("`,`", $fA),
      implode(',', $fV)
    );

    $this->Q($sql);
    return ($this->mysqli->insert_id);
  }

  public function Q($query) {

    if ($this->log) logger::sql($query);
    if ($result = $this->mysqli->query($query)) return $result;

    /****************************************
     * You are here because there was an error **/
    $message = sprintf(
      "Error : MySQLi : %s\nError : MySQLi : %s",
      $query,
      $this->mysqli->error
    );

    logger::sql($message);
    foreach (debug_backtrace() as $e) {
      logger::info(sprintf('%s(%s)', $e['file'] ?? '?file', $e['line'] ?? '?line'));
    }

    throw new Exception($message);
  }

  public function quote(?string $val): string {

    if ('NULL' == $val) return $val;
    return sprintf('"%s"', $this->escape($val));
  }

  public function result($query): ?dbResult {
    try {

      $dbResult = new dbResult($this->Q($query), $this);
      return $dbResult;
    } catch (Exception $e) {

      logger::info(sprintf('<%s> %s', mysqli_error($this->mysqli), __METHOD__));
      throw new Exceptions\SQLException;
    }

    return null;
  }

  public function table_exists(string $table): bool {
    $sql = sprintf(
      'SELECT * FROM information_schema.tables WHERE table_schema = "%s" AND table_name = "%s" LIMIT 1',
      $this->escape($this->dbname),
      $this->escape($table)

    );

    if ($result = $this->result($sql)) {
      if ($dto = $result->dto()) {
        return true;
      }
    }

    return (false);
  }

  public function Update($table, $a, $scope, $flushCache = true) {
    if ((bool)$flushCache) $this->flushCache();

    $aX = [];
    foreach ($a as $k => $v) {

      // $aX[] = sprintf('`%s` = %s', $k, $this->quote($v));
      $aX[] = sprintf('`%s` = %s', $k, $this->quote((new dbSanitize($v))()));
    }

    $sql = sprintf('UPDATE `%s` SET %s %s', $table, implode(', ', $aX), $scope);
    return $this->Q($sql);
  }
}
