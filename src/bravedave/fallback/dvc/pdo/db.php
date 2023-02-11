<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc\pdo;

use config;
use bravedave\dvc\Exceptions\DBNotConfigured;
use bravedave\dvc\logger;
use PDO, PDOException;
use PDOStatement;

class db {

  protected $_dsn = null;

  protected $_connection = null;

  protected function __connected(): bool {

    if ($this->_connection) return true;
    try {

      if ( !class_exists('\PDO')) {

        print 'the PDO class is not found, install it to continue';
        die;

      }
      $this->_connection = new PDO($this->_dsn, config::$DB_USER, config::$DB_PASS);
      $this->_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      logger::info(sprintf('<%s> %s', 'opened', __METHOD__));
    } catch (PDOException $e) {

      logger::info(sprintf('<Error ..> %s', __METHOD__));
      logger::info(sprintf('<PDO Error : %s> %s', $e->getMessage(), __METHOD__));
      die();
    }

    return true;
  }

  public function __construct() {

    if ('sqlite' == config::$DB_TYPE) {
      $_path = sprintf('%s%sdb.sqlite', config::dataPath(), DIRECTORY_SEPARATOR);
      $this->_dsn = sprintf(
        '%s:%s',
        config::$DB_TYPE,
        $_path
      );

      logger::info(sprintf('<%s> %s', $this->_dsn, __METHOD__));
    } elseif ('mysql' == config::$DB_TYPE) {
      $this->_dsn = sprintf(
        '%s:dbname=%s;host=%s;charset=UTF8',
        config::$DB_TYPE,
        config::$DB_NAME,
        config::$DB_HOST
      );

      logger::info(sprintf('<%s> %s', $this->_dsn, __METHOD__));
    } else {

      throw new DBNotConfigured;
    }
  }

  public function __destruct() {

    $this->_connection = null;
    logger::info(sprintf('<%s> %s', 'closed', __METHOD__));
  }

  public function exec(string $sql): ?int {

    if (!$this->__connected()) return null;
    return $this->_connection->exec($sql);

    return null;
  }

  public function query(string $sql, array $parameters = []): ?PDOStatement {

    if (!$this->__connected()) return null;
    if ($statement = $this->_connection->prepare($sql)) {
      $statement->setFetchMode(PDO::FETCH_OBJ);
      $statement->execute($parameters);
      return $statement;
    }

    return null;
  }

  public function insert(string $table, array $data): ?int {

    if (!$this->__connected()) return null;

    $flds = [];
    $vals = [];
    foreach ($data as $k => $v) {
      $flds[] = $k;
      $vals[] = sprintf(':%s', $k);
    }

    $sql = sprintf(
      'INSERT INTO `%s`(%s) VALUES(%s)',
      $table,
      implode(',', $flds),
      implode(',', $vals)
    );

    $b = $this->_connection->prepare($sql);
    if ($b->execute($data)) {
      return $this->_connection->lastInsertId();
    }

    return null;
  }

  public function updateByID(string $table, array $data, int $id): bool {

    if (!$this->__connected()) return null;

    $a = [];
    foreach ($data as $k => $v) {
      $a[] = sprintf('%s=:%s', $k, $k);
    }

    $sql = sprintf(
      'UPDATE `%s` SET %s WHERE id=:id',
      $table,
      implode(',', $a)
    );

    $b = $this->_connection->prepare($sql);
    $data['id'] = $id;  // so you can never pass id in !, we don't support fields named id

    if (!($res = $b->execute($data))) {
      logger::info(sprintf('<error %s> %s', $b->errorCode(), __METHOD__));
    }

    return $res;
  }
}
