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

use Closure;
use mysqli_result;
use SQLite3Result;
use stdClass;

/**
 * Class dto
 *
 * A Data Transfer Object (DTO) class that extends PHP's stdClass.
 * This class is designed to facilitate the transfer of data between layers of an application.
 * It provides methods for populating the object with data, exporting it as an array, 
 * serializing it to JSON, and converting it to a string for debugging purposes.
 *
 * Features:
 * - Dynamic property population from an input row (associative array or object).
 * - Conversion of the DTO to an array or JSON format.
 * - String representation for debugging.
 * - Supports invoking the object to fetch the first row of a query as a DTO.
 *
 * @package bravedave\dvc
 */
class dto extends stdClass {

  /**
   * @var int $id Default ID property for the DTO.
   */
  public $id = 0;

  /**
   * Constructor
   *
   * Initializes the DTO by populating it with data from the provided row.
   *
   * @param array|object|null $row Optional data to populate the DTO.
   */
  public function __construct($row = null) {

    $this->populate($row);
  }

  /**
   * Invoke method
   *
   * Allows the object to be invoked as a function to fetch the first row of a query as a DTO.
   *
   * @param int|string $sql The SQL query or identifier.
   * @param Closure|null $func Optional callback function to process the query results.
   * @param string|null $template Optional template class for the DTO.
   * @return self|null Returns the first row as a DTO or null if no rows are found.
   */
  public function __invoke(int|string|SQLite3Result|mysqli_result $sql, Closure|null $func = null, string|null $template = null): ?self {

    if (is_string($sql) || $sql instanceof mysqli_result || $sql instanceof SQLite3Result) {

      if (is_null($template)) $template = $this::class;
      if ($dtoSet = (new dtoSet)($sql, $func, $template)) {

        if ($dto = array_shift($dtoSet)) return $dto;
      }
    }

    return null;
  }

  /**
   * Populate the DTO with data
   *
   * Dynamically assigns properties to the DTO based on the provided row.
   *
   * @param array|object|null $row Data to populate the DTO. Can be an associative array or an object.
   * @return void
   */
  protected function populate($row = null) {

    // logger::info(sprintf('<allowing dynamic properties> %s', __METHOD__));
    if (!(is_null($row))) {

      foreach ($row as $k => $v) {

        $this->{$k} = $v;
      }
    }
  }

  /**
   * Export DTO as an array
   *
   * Converts the DTO's properties into an associative array.
   *
   * @return array An associative array of the DTO's properties.
   */
  public function toArray(): array {

    return get_object_vars($this);
  }

  /**
   * JSON serialization hook
   *
   * Converts the DTO into a format suitable for JSON serialization.
   *
   * @return mixed The DTO's properties as an associative array.
   */
  public function jsonSerialize(): mixed {

    return $this->toArray();
  }


  /**
   * Deprecated string representation
   *
   * Converts the DTO's properties into a string representation.
   * This method is marked as deprecated and should not be used in new code.
   *
   * @deprecated
   * @return string A string representation of the DTO's properties.
   */
  #[\Deprecated]
  public function toString() {

    $s = [];
    foreach ($this as $k => $v) {

      $s[] = sprintf('%s = %s', $k, $v);
    }

    return implode(PHP_EOL, $s);
  }

  /**
   * String representation for debugging
   *
   * Converts the DTO into a JSON string with pretty-print formatting.
   *
   * @return string A JSON string representation of the DTO.
   */
  public function __toString(): string {

    return json_encode($this, JSON_PRETTY_PRINT);
  }
}
