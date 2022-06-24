<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/


namespace dvc;

use Response;

class Json {
  protected $_json = [];
  protected $dumpOnDestruct = true;

  static public function nak($description): Json {
    return (new self(['response' => 'nak', 'description' => $description]));
  }

  static public function ack($description): Json {
    return (new self(['response' => 'ack', 'description' => $description]));
  }

  static public function read(string $path): object {
    return file_exists($path) ?
      json_decode(file_get_contents($path)) :
      (object)[];
  }

  static public function write(string $path, object $object) {
    return file_put_contents(
      $path,
      json_encode(
        $object,
        JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT

      )

    );
  }

  function __construct($a = null) {
    if (!is_null($a))
      $this->_json = (array)$a;
  }

  public function add($key, $data) {
    $this->_json[$key] = $data;
    return ($this);  // chain

  }

  public function append($data) {
    $this->_json[] = $data;
    return ($this);  // chain

  }

  public function __destruct() {
    if ($this->dumpOnDestruct) {
      $response = json_encode($this->_json);
      Response::json_headers(0, \strlen($response));
      print $response;
    }
  }

  public function count() {
    return count($this->_json);
  }

  public function dump() {
    $this->dumpOnDestruct = false;
    \sys::dump($this->_json);
  }

  public function merge($data) {
    $a = array_merge($this->_json, $data);
    $this->_json[] = $a;
    return ($this);  // chain

  }

  public function prepend($data) {
    array_unshift($this->_json, $data);
    return ($this);  // chain

  }

  public function print() {
    $this->dumpOnDestruct = false;
    print json_encode($this->_json);
  }

  public function toArray() {
    $this->dumpOnDestruct = false;
    return $this->_json;
  }
}
