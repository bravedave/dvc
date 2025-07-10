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

use bravedave\dvc\Exceptions\{DBNameIsNull, DBNotConfigured};
use config, dvc;
use mysqli_result;
use RuntimeException;
use SQLite3Result;

/**
 * Class dao
 *
 * An abstract Data Access Object (DAO) class that provides a base implementation for interacting with a database.
 * This class is designed to be extended by specific DAO implementations for various database tables.
 *
 * Features:
 * - Provides methods for CRUD operations (Create, Read, Update, Delete).
 * - Supports caching mechanisms for database queries.
 * - Includes utility methods for table structure validation and dynamic data handling.
 * - Abstracts database-specific operations to simplify interaction with different database types.
 *
 * Key Methods:
 * - `create()`: Creates a new Data Transfer Object (DTO) for the table.
 * - `getByID($id)`: Retrieves a record by its ID.
 * - `getAll($fields, $order)`: Retrieves all records with optional field selection and ordering.
 * - `Insert($data)`: Inserts a new record into the database.
 * - `Update($data, $condition)`: Updates records based on a condition.
 * - `delete($id)`: Deletes a record by its ID.
 * - `Result($query)`: Executes a query and returns a database result object.
 * - `TableExists($table)`: Checks if a table exists in the database.
 *
 * Caching:
 * - Supports caching of query results using APC or other caching mechanisms.
 * - Provides methods for generating and deleting cache keys.
 *
 * Database Structure:
 * - Includes methods for checking and validating table structures.
 * - Dynamically creates DTOs based on table column definitions.
 *
 * Usage:
 * - Extend this class to implement specific DAO functionality for a database table.
 * - Override methods like `before()` or `structure()` to customize behavior.
 *
 * @package bravedave\dvc
 */
abstract class dao {
  protected $_sql_getByID = 'SELECT * FROM %s WHERE id = %d';
  protected $_sql_getAll = 'SELECT %s FROM %s %s';

  protected $_db_name = null;
  protected $_db_cache_prefix = null;
  protected $_db_allways_check_structure = true;
  protected $template = null;

  public $db;
  public $log = false;

  protected function cacheKey(int $id, string $field = ''): string {

    if ($field) {

      return sprintf(
        '%s_%s_%s_%s',
        $this->cachePrefix(),
        $this->db_name(),
        $id,
        $field
      );
    } else {

      return sprintf(
        '%s_%s_%s',
        $this->cachePrefix(),
        $this->db_name(),
        $id
      );
    }
  }

  protected function cacheKey_delete(int $id, string $field = '') {

    return sprintf('/%s/', $this->cacheKey($id, $field));
  }

  protected function cachePrefix() {

    if ($this->_db_cache_prefix) return db::cachePrefix() . $this->_db_cache_prefix;
    return db::cachePrefix();
  }

  protected function before() {
    /**
     * Abstract method placeholder for use by the child class.
     * This method is called at the end of __construct()
     *
     * avoid replacing the default __construct method - use before instead
     *
     * Inspired by something I read in the fuelPHP documentation
     * this method is called at the end of __construct and can
     * be used to modify the _controller class
     */
  }

  protected function check() {

    if ($dbc = $this->structure()) return $dbc->check();
    return false;
  }

  protected function _create(): dto {

    if ('sqlite' == config::$DB_TYPE) {

      $fieldList = $this->db->fieldList($this->db_name());
      $o = new dto;
      foreach ($fieldList as $f) {

        $o->{$f->name} = $f->dflt_value;
      }

      return $o;
    }

    //~ $fields = $this->db->fieldList( $this->table);
    //~ $struct = $this->db->fetchFields( $this->db_name());
    $res = $this->Result(sprintf('SHOW COLUMNS FROM %s', $this->db_name()));
    $dtoSet = $res->dtoSet(function ($dto) {
      /*
				in:

				[Field] => id
				[Type] => bigint(20)
				[Null] => NO
				[Key] => PRI
				[Default] =>
				[Extra] => auto_increment

			*/

      //~ $field->Dec
      $dto->Len = 0;
      $type = strtoupper(preg_replace('@\(.*$@', '', $dto->Type));

      if ('BIGINT' == $type || 'SMALLINT' == $type || 'TINYINT' == $type || 'INT' == $type) {

        $dto->Len = trim(preg_replace('@^.*\(@', '', $dto->Type), ') ');
        $dto->Type = $type;
        $dto->Default = (int)$dto->Default;
      } elseif ('DATE' == $type || 'DATETIME' == $type) {

        $dto->Type = $type;
      } elseif ('MEDIUMTEXT' == $type || 'TEXT' == $type) {

        $dto->Type = $type;
      } elseif ('VARCHAR' == $type || 'VARBINARY' == $type) {

        $dto->Len = trim(preg_replace('@^.*\(@', '', $dto->Type), ') ');
        $dto->Type = $type;
      }

      return $dto;
    });

    $o = new dto;
    foreach ($dtoSet as $dto) {

      $o->{$dto->Field} = $dto->Default;
    }

    return $o;
  }

  protected function structure($name = null) {

    return false;
  }

  protected function TableChecks() {

    if (!$this->db->valid()) return false;

    if (is_null($this->_db_name)) return false;

    if ($this->_db_allways_check_structure) {

      return $this->check();
    } elseif (!($this->TableExists())) {

      return $this->check();
    }

    return false;
  }

  protected function TableExists($table = null): bool {

    if (is_null($table)) $table = $this->db_name();
    if (is_null($table)) return false;

    // logger::info( "checking for: $table" );

    if ('sqlite' == config::$DB_TYPE) {

      $sql = sprintf(
        'SELECT `name` FROM `sqlite_master` WHERE `type` = %s AND `name` = %s',
        $this->quote('table'),
        $this->quote($table)
      );

      if ($res = $this->Result($sql)) {

        return (bool)$res->dto();
      }
    } else {

      $sql = sprintf(
        'SELECT
          CASE WHEN (
            SELECT
              COUNT(*)
            FROM
              information_schema.TABLES
            WHERE
              TABLE_SCHEMA = %s
              AND TABLE_NAME = %s
            ) < 1 THEN 1
          ELSE 0
          END t',
        $this->quote('DATABASENAME'),
        $this->quote($table)
      );

      if ($res = $this->Result($sql)) {

        if ($row = $res->fetch()) {

          if ($row['t'] == 1) return true;
        }
      }
    }

    return false;
  }

  function __construct(db|null $db = null) {

    if (!config::checkDBconfigured()) {

      // logger::info( sprintf('<Call the doctor I think I\'m gonna crash> %s', __METHOD__));
      // logger::info( sprintf('<The doctor say he\'s coming but you gotta create a config file buddy> %s', __METHOD__));
      throw new DBNotConfigured;
    }

    $this->db = is_null($db) ? \sys::dbi() : $db;

    $this->TableChecks();
    $this->before();
  }

  public function __invoke(int $id): ?dto {

    if ($dto = $this->getByID($id)) {

      if (method_exists($this, 'getRichData')) {

        /** @disregard P1013 Undefined method */
        return $this->getRichData($dto);
      }
      return $dto;
    }

    return null;
  }

  public function audit(string $event, array $data, int $id): void {
  }

  public static function asDTO($res, $template = null): array {

    return $res->dtoSet(null, $template);
  }

  public function cacheDelete(int $id): void {

    if (config::$DB_CACHE == 'APC') {

      $cache = cache::instance();
      $key = $this->cacheKey_delete($id);
      $cache->delete($key, true);
    }
  }

  public function create() {    /* returns a new bravedave\dvc\dto of the file */

    if (is_null($this->template)) return ($this->_create());
    return new $this->template;
  }

  public function count(): int {

    if (is_null($this->_db_name)) throw new DBNameIsNull;

    $sql = sprintf('SELECT COUNT(*) as i FROM `%s`', $this->_db_name);
    if ($dto = (new dto)($sql)) return ($dto->i ?? 0);
    return 0;
  }

  public function db_name(): string {

    return $this->_db_name;
  }

  public static function dbTimeStamp() {

    return \db::dbTimeStamp();
  }

  public function delete($id) {

    if (is_null($this->_db_name)) throw new DBNameIsNull;

    $this->db->log = $this->log;
    $this->Q(sprintf('DELETE FROM %s WHERE id = %d', $this->_db_name, (int)$id));
    $this->cacheDelete($id);

    $this->audit('delete', [],  $id);
  }

  public function dtoSet($res, $func = null): array {

    if ($res instanceof dbResult || $res instanceof sqlite\dbResult) {

      return $res->dtoSet($func, $this->template);
    } elseif ($res instanceof dvc\dbResult || $res instanceof dvc\sqlite\dbResult) {

      logger::deprecated('calling older instances of dvc\dbResult');
      return $res->dtoSet($func, $this->template);
    } else {

      throw new RuntimeException(sprintf('"Argument 1 passed to %s must be an instance of dvc\dbResult or dvc\sqlite\dbResult', __METHOD__));
    }
  }

  public function escape($s) {

    return $this->db->escape($s);
  }

  public function execute_query(string $query, ?array $params = null): mysqli_result|SQLite3Result|bool {
    
    return $this->db->execute_query($query, $params);
  }
  
  public function getAll($fields = '*', $order = '') {

    if (is_null($this->_db_name)) throw new DBNameIsNull;

    $this->db->log = $this->log;
    $sql = sprintf($this->_sql_getAll, $fields, $this->db_name(), $order);
    return $this->Result($sql);
  }

  public function getByID($id) {

    if (is_null($this->_db_name)) throw new DBNameIsNull;

    if (config::$DB_CACHE == 'APC') {

      $cache = cache::instance();
      $key = $this->cacheKey($id);
      if ($dto = $cache->get($key)) {

        /**
         * The problem is there are some dirty unserializable dto's,
         * particularly in CMS (private repository) which is very old code
         *
         * so, check the type matches ..
         * debug is currently on for this => bravedave\dvc\config::$DB_CACHE_DEBUG_TYPE_CONFLICT = true;
         *
         */
        if ($thisType = get_class($dto)) {

          $thisType = $thisType; // namespace will have preceding \, get_class will come from root
          $approvedType = ltrim($this->template ? $this->template : 'bravedave\dvc\dto', '\\');
          if ($thisType == $approvedType) {

            if (config::$DB_CACHE_DEBUG) logger::debug(sprintf('<type check %s:%s> %s[\]%s', $thisType, $approvedType, get_class($this), __METHOD__));
            return ($dto);
          } elseif (config::$DB_CACHE_DEBUG || config::$DB_CACHE_DEBUG_TYPE_CONFLICT) {

            logger::debug(sprintf('<fails type check %s:%s> %s[\]%s', $thisType, $approvedType, get_class($this), __METHOD__));
          }
        } elseif (config::$DB_CACHE_DEBUG || config::$DB_CACHE_DEBUG_TYPE_CONFLICT) {

          logger::debug(sprintf('<cached object has no type> %s[\]%s', get_class($this), __METHOD__));
        }
      }
    } else {

      if (config::$DB_CACHE_DEBUG) logger::debug(sprintf('<cache not enabled> %s', __METHOD__));
    }

    $this->db->log = $this->log;

    $sql = sprintf($this->_sql_getByID, $this->_db_name, (int)$id);
    if ($dto = (new dto)($sql, null, $this->template)) {

      if (config::$DB_CACHE == 'APC') $cache->set($key, $dto);
      return $dto;
    }

    // if ($res = $this->Result(sprintf($this->_sql_getByID, $this->_db_name, (int)$id))) {

    //   if ($dto = $res->dto($this->template)) {

    //     if (config::$DB_CACHE == 'APC') $cache->set($key, $dto);
    //   }

    //   return $dto;
    // }

    return false;
  }

  public function getFieldByID($id, $fld) {

    if (is_null($this->_db_name)) throw new DBNameIsNull;

    if (config::$DB_CACHE == 'APC') {

      $cache = cache::instance();
      $key = $this->cacheKey($id, $fld);
      if ($v = $cache->get($key)) return $v;
    }

    $this->db->log = $this->log;
    if ($res = $this->Result(sprintf($this->_sql_getByID, $this->_db_name, (int)$id))) {

      if ($dto = $res->dto($this->template)) {

        if (config::$DB_CACHE == 'APC') $cache->set($key, $dto->{$fld});
        return $dto->{$fld};
      }
    }

    return false;
  }

  // public function getRichData(dto $dto): ?dto {

  //   return $dto;
  // }

  public function Insert($a) {

    if (is_null($this->db_name())) throw new DBNameIsNull;

    $a = (array)$a;
    if (isset($a['id'])) unset($a['id']);

    $this->db->log = $this->log;
    $id = $this->db->Insert($this->db_name(), $a);

    $this->audit('insert', $a,  $id);
    return $id;
  }

  public function Update($a, $condition, $flushCache = true) {

    if (is_null($this->db_name())) throw new DBNameIsNull;

    $this->db->log = $this->log;
    return $this->db->Update($this->db_name(), $a, $condition, $flushCache);
  }

  public function UpdateByID($a, $id) {

    if (is_null($this->db_name())) throw new DBNameIsNull;
    $this->cacheDelete($id);
    $ret = $this->Update($a, sprintf('WHERE id = %d', $id), $flushCache = false);

    $this->audit('update', $a,  $id);
    return $ret;
  }

  /**
   * runs a query and returns a dbResult object
   * the opbject maybe a dvc\dbResult or a dvc\sqlite\dbResult
   */
  public function Result($query) {

    $this->db->log = $this->log;
    return $this->db->Result($query);
  }

  public function Q($query) {

    $this->db->log = $this->log;
    return $this->db->Q($query);
  }

  public function quote($s) {

    return $this->db->quote($s);
  }
}
