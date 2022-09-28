<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc\dao;

use dvc;

abstract class _dao {
  protected $_sql_getByID = 'SELECT * FROM %s WHERE id = %d';
  protected $_sql_getAll = 'SELECT %s FROM %s %s';

  protected $_db_name = null;
  protected $_db_cache_prefix = null;
  protected $_db_allways_check_structure = true;
  protected $template = null;

  public $db;
  public $log = false;

  function __construct(dvc\dbi $db = null) {

    if (!\config::checkDBconfigured()) {
      // \sys::logger( sprintf('<Call the doctor I think I\'m gonna crash> %s', __METHOD__));
      // \sys::logger( sprintf('<The doctor say he\'s coming but you gotta create a config file buddy> %s', __METHOD__));
      throw new dvc\Exceptions\DBNotConfigured;
    }

    $this->db = is_null($db) ? \sys::dbi() : $db;

    $this->TableChecks();
    $this->before();
  }

  public function __invoke(int $id): ?dto\_dto {

    if ($dto = $this->getByID($id)) {
      if (method_exists($this, 'getRichData')) {

        return $this->getRichData($dto);
      }

      return $dto;
    }

    return null;
  }

  public static function asDTO($res, $template = null): array {
    return $res->dtoSet(null, $template);
  }

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
    if ($this->_db_cache_prefix) {
      return \sys::dbCachePrefix() . $this->_db_cache_prefix;
    }

    return \sys::dbCachePrefix();
  }

  protected function before() {
    /*
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
    if ($dbc = $this->structure()) {
      return $dbc->check();
    }

    return false;
  }

  protected function _create() {
    if ('sqlite' == \config::$DB_TYPE) {
      $fieldList = $this->db->fieldList($this->db_name());
      $o = new dvc\dao\dto\dto;
      foreach ($fieldList as $f) {
        $o->{$f->name} = $f->dflt_value;
      }

      return ($o);
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

      return ($dto);
    });

    $o = new dvc\dao\dto\dto;
    foreach ($dtoSet as $dto) {
      $o->{$dto->Field} = $dto->Default;
    }

    return ($o);
  }

  public function cacheDelete(int $id): void {
    if (\config::$DB_CACHE == 'APC') {
      $cache = dvc\cache::instance();
      $key = $this->cacheKey_delete($id);
      $cache->delete($key, true);
    }
  }

  public function create() {    /* returns a new dvc\dao\dto of the file */
    if (is_null($this->template)) {
      return ($this->_create());
    }

    return new $this->template;
  }

  public function count(): int {
    if (is_null($this->_db_name)) throw new dvc\Exceptions\DBNameIsNull;

    if ($res = $this->Result(sprintf('SELECT COUNT(*) as i FROM `%s`', $this->_db_name))) {
      if ($dto = $res->dto()) {
        return $dto->i;
      }
    }

    return 0;
  }

  public function db_name() {
    return ($this->_db_name);
  }

  public static function dbTimeStamp() {
    return (\db::dbTimeStamp());
  }

  public function delete($id) {
    if (is_null($this->_db_name)) {
      throw new dvc\Exceptions\DBNameIsNull;
    }

    $this->db->log = $this->log;
    $this->Q(sprintf('DELETE FROM %s WHERE id = %d', $this->_db_name, (int)$id));

    $this->cacheDelete($id);
    // if (\config::$DB_CACHE == 'APC') {
    //   $cache = dvc\cache::instance();
    //   $key = $this->cacheKey_delete($id);
    //   $cache->delete($key, true);
    // }
  }

  public function dtoSet($res, $func = null): array {
    if ($res instanceof dvc\dbResult || $res instanceof dvc\sqlite\dbResult) {
      return $res->dtoSet($func, $this->template);
    } else {
      throw new \Exception(sprintf('"Argument 1 passed to %s must be an instance of dvc\dbResult or dvc\sqlite\dbResult', __METHOD__));
    }
  }

  public function escape($s) {
    return ($this->db->escape($s));
  }

  public function getAll($fields = '*', $order = '') {
    if (is_null($this->_db_name)) throw new dvc\Exceptions\DBNameIsNull;

    $this->db->log = $this->log;
    return ($this->Result(sprintf($this->_sql_getAll, $fields, $this->db_name(), $order)));
  }

  public function getByID($id) {
    if (is_null($this->_db_name)) {
      throw new dvc\Exceptions\DBNameIsNull;
    }

    if (\config::$DB_CACHE == 'APC') {
      $cache = dvc\cache::instance();
      $key = $this->cacheKey($id);
      if ($dto = $cache->get($key)) {
        /**
         * The problem is there are some dirty unserializable dto's,
         * particularly in CMS (private repository) which is very old code
         *
         * so, check the type matches ..
         * debug is currently on for this => dvc\core\config::$DB_CACHE_DEBUG_TYPE_CONFLICT = true;
         *
         */
        if ($thisType = get_class($dto)) {
          $thisType = $thisType; // namespace will have preceding \, get_class will come from root
          $approvedType = ltrim($this->template ? $this->template : __NAMESPACE__ . '\dto\dto', '\\');
          if ($thisType == $approvedType) {
            if (\config::$DB_CACHE_DEBUG) \sys::logger(sprintf('<type check %s:%s> %s[\]%s', $thisType, $approvedType, get_class($this), __METHOD__));
            return ($dto);
          } elseif (\config::$DB_CACHE_DEBUG || \config::$DB_CACHE_DEBUG_TYPE_CONFLICT) {
            \sys::logger(sprintf('<fails type check %s:%s> %s[\]%s', $thisType, $approvedType, get_class($this), __METHOD__));
          }
        } elseif (\config::$DB_CACHE_DEBUG || \config::$DB_CACHE_DEBUG_TYPE_CONFLICT) {
          \sys::logger(sprintf('<cached object has no type> %s[\]%s', get_class($this), __METHOD__));
        }
      }
    } else {
      if (\config::$DB_CACHE_DEBUG) \sys::logger(sprintf('<cache not enabled> %s', __METHOD__));
    }

    $this->db->log = $this->log;
    if ($res = $this->Result(sprintf($this->_sql_getByID, $this->_db_name, (int)$id))) {
      if ($dto = $res->dto($this->template)) {
        if (\config::$DB_CACHE == 'APC') {
          $cache->set($key, $dto);
        }
      }

      return ($dto);
    }

    return false;
  }

  public function getFieldByID($id, $fld) {
    if (is_null($this->_db_name)) {
      throw new dvc\Exceptions\DBNameIsNull;
    }

    if (\config::$DB_CACHE == 'APC') {
      $cache = dvc\cache::instance();
      $key = $this->cacheKey($id, $fld);
      if ($v = $cache->get($key)) {
        return ($v);
      }
    }

    $this->db->log = $this->log;
    if ($res = $this->Result(sprintf($this->_sql_getByID, $this->_db_name, (int)$id))) {
      if ($dto = $res->dto($this->template)) {
        if (\config::$DB_CACHE == 'APC') {
          $cache->set($key, $dto->{$fld});
        }

        return ($dto->{$fld});
      }
    }

    return false;
  }

  // public function getRichData(dto\_dto $dto): ?dto\_dto {
  //   return $dto;
  // }

  public function Insert($a) {
    if (is_null($this->db_name())) {
      throw new dvc\Exceptions\DBNameIsNull;
    }

    $a = (array)$a;
    if (isset($a['id'])) {
      unset($a['id']);
    }

    $this->db->log = $this->log;
    return ($this->db->Insert($this->db_name(), $a));
  }

  public function Update($a, $condition, $flushCache = true) {
    if (is_null($this->db_name()))
      throw new dvc\Exceptions\DBNameIsNull;

    $this->db->log = $this->log;
    return ($this->db->Update($this->db_name(), $a, $condition, $flushCache));
  }

  public function UpdateByID($a, $id) {
    if (is_null($this->db_name()))
      throw new dvc\Exceptions\DBNameIsNull;

    $this->cacheDelete($id);
    // if (\config::$DB_CACHE == 'APC') {
    //   $cache = dvc\cache::instance();
    //   $key = $this->cacheKey_delete($id);
    //   $cache->delete($key, true);
    // }

    return ($this->Update($a, sprintf('WHERE id = %d', $id), $flushCache = false));
  }

  public function Result($query) {

    $this->db->log = $this->log;
    return $this->db->Result($query);
  }

  public function Q($query) {
    $this->db->log = $this->log;
    return ($this->db->Q($query));
  }

  public function quote($s) {

    return $this->db->quote($s);
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

    //~ \sys::logger( "checking for: $table" );

    if ('sqlite' == \config::$DB_TYPE) {

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
}
