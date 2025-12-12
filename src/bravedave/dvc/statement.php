<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * this should set the preferred database statement
 * for the application, so that we can use it in a consistent way
 * across MySQL and SQLite3
 *
 * a dtoSet is the preferred data handling method
 * this allows us to use a Closure to process each dto
 * and a template string to format the output
 *
 * usage:
 * $stmt = new statement('SELECT * FROM table WHERE id = ?', $db);
 * $stmt->bind([1]);
 * $result = $stmt->dtoSet();
 * if ($result) {
 *   foreach ($result as $dto) {
 *     // process each dto
 *   }
 * }
 * and close or unset the statement when done
 * $stmt->close();
 * or
 * unset($stmt);
 * 
 * if can be used as equivalent to a dtoSet directly
 * $stmt = new statement('SELECT * FROM table WHERE id = ?', $db);
 * $dtoSet = $stmt([1]);
 * if ($dtoSet) {
 *   foreach ($dtoSet as $dto) {
 *     // process each dto
 *   }
 * }
 * 
 * $stmt([1], function($dto) {
 *   // process each dto
 * }, 'template string');
*/

namespace bravedave\dvc;

use Closure;
use mysqli_result;
use mysqli_stmt;
use RuntimeException;
use SQLite3Result;
use SQLite3Stmt;

class statement {

  /**
   * this is a wrapper class for a MySQL or SQLite3 statement
   */
  protected SQLite3Stmt|mysqli_stmt|null $_statement;

  public function __construct(string $sql, sqlite\db|dbi|null $db = null) {

    if (is_null($db)) $db = \sys::dbi();
    $this->_statement = $db->prepare($sql);
  }

  /**
   * on destruct, close the statement if it is open
   */
  public function __destruct() {

    $this->close();
  }

  public function __invoke(array $values = [], Closure|null $func = null, string|null $template = null): array {

    if ($result = $this->execute($values)) return (new dtoSet)($result, $func, $template);
    return [];
  }

  /**
   * bind a parameter to the statement
   * be sure to use the correct style for the database
   * the syntax will be mysql style - i.e. 'SELECT * FROM table WHERE id = ?'
   * SQLite will use the same syntax
   * - if it is an sqlite statement use conversion syntax
   */
  public function bind(array $values): bool {

    if ($this->_statement instanceof SQLite3Stmt) {

      // SQLite uses positional parameters, index starts at 1
      foreach ($values as $i => $value) {

        $paramType = SQLITE3_TEXT;
        if (is_int($value)) {
          $paramType = SQLITE3_INTEGER;
        } elseif (is_string($value)) {
          $paramType = SQLITE3_TEXT;
        } elseif (is_float($value)) {
          $paramType = SQLITE3_FLOAT;
        } elseif (is_null($value)) {
          $paramType = SQLITE3_NULL;
        }

        if (!$this->_statement->bindValue($i + 1, $value, $paramType)) {

          // if binding fails, return false
          return false;
        }
      }

      return true;
    } elseif ($this->_statement instanceof mysqli_stmt) {

      // MySQLi uses types and references
      $types = '';
      $bindParams = [];
      foreach ($values as $value) {
        if (is_int($value)) {
          $types .= 'i';
        } elseif (is_float($value)) {
          $types .= 'd';
        } elseif (is_null($value)) {
          $types .= 's';
        } else {
          $types .= 's';
        }
        $bindParams[] = $value;
      }
      // Use argument unpacking for bind_param
      return $this->_statement->bind_param($types, ...$bindParams);
    }

    return false;
  }

  /**
   * closes the statement
   */
  public function close(): void {

    if ($this->_statement instanceof SQLite3Stmt) {
      $this->_statement->close();
    } elseif ($this->_statement instanceof mysqli_stmt) {
      $this->_statement->close();
    }

    $this->_statement = null;
  }

  /**
   * and the preferred restult would be a dtoSet
   * use like this:
   * $stmt = new statement('SELECT * FROM table WHERE id = ?', $db);
   * $stmt->bind([1]);
   * $result = $stmt->dtoSet();
   * if ($result) {
   *   foreach ($result as $dto) {
   *     // process each dto
   *   }
   * }
   *
   * dtoSet can accept a Closure to process each dto
   * and a template string to format the output
   */
  public function dtoSet(Closure|null $func = null, string|null $template = null): array {

    if ($result = $this->execute()) {

      return (new dtoSet)($result, $func, $template);
    }

    return [];
  }

  /**
   * the use is like this:
   * $stmt = new statement('SELECT * FROM table WHERE id = ?', $db);
   * $stmt->bind([1]);
   * $result = $stmt->execute();
   * if ($result) {
   *   while ($row = $result->fetch_assoc()) {
   *     // process each row
   *   }
   * }
   *
   * $stmt->execute() may accept the bind values directly
   * $stmt->execute([1]);
   */
  public function execute(array $values = []): bool|sqlite\dbResult|dbResult {

    // check if the statement is still open
    if (is_null($this->_statement)) {
      throw new RuntimeException('Statement is closed or not prepared.');
    }

    if ($values) {

      if (!$this->bind($values)) return false;
    }

    if ($this->_statement instanceof SQLite3Stmt) {

      $result = $this->_statement->execute();
      if ($result instanceof SQLite3Result) {

        // Wrap in SQLite\dbResult
        return new sqlite\dbResult($result);
      }
      return false;
    } elseif ($this->_statement instanceof mysqli_stmt) {

      if ($this->_statement->execute()) {

        $result = $this->_statement->get_result();

        if ($result instanceof \mysqli_result) {

          // Wrap in dbResult
          return new dbResult($result);
        }
      }
      return false;
    }

    return false;
  }
}
