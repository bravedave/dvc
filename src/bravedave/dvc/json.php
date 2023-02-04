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

class json {
  protected $_json = [];
  protected $dumpOnDestruct = true;

  static public function nak(string $description): Json {

    return new self([
      'response' => 'nak',
      'description' => $description
    ]);
  }

  static public function ack(string $description): Json {
    return new self([
      'response' => 'ack',
      'description' => $description
    ]);
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

  /**
   *
   * add keyed data to the object
   *
   * @param string $key
   * @param array|object $data
   * @return $this
   */
  public function add(string $key, null|string|array|object $data): self {

    $this->_json[$key] = $data;
    return $this;  // chain
  }

  public function append(array|object $data): self {

    $this->_json[] = $data;
    return $this;  // chain
  }

  public function __destruct() {

    if ($this->dumpOnDestruct) {

      $response = json_encode($this->_json);
      Response::json_headers(0, strlen($response));
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

  public function merge($data): self {

    $a = array_merge($this->_json, $data);
    $this->_json[] = $a;
    return $this;  // chain
  }

  public function prepend(array|object $data): self {

    array_unshift($this->_json, $data);
    return $this;  // chain
  }

  public function print(): void {

    $this->dumpOnDestruct = false;
    print json_encode($this->_json);
  }

  public function toArray(): array {

    $this->dumpOnDestruct = false;
    return $this->_json;
  }
}
