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

  static public function nak(string $description, mixed $data = null): json {

    $a = [
      'response' => 'nak',
      'description' => $description
    ];
    if (!is_null($data)) $a['data'] = $data;

    return new self($a);
  }

  static public function ack(string $description, mixed $data = null): json {

    $a = [
      'response' => 'ack',
      'description' => $description
    ];
    if (!is_null($data)) $a['data'] = $data;

    return new self($a);
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

  public function __construct($a = null) {

    if (!is_null($a)) $this->_json = (array)$a;
  }

  public function __destruct() {

    if ($this->dumpOnDestruct) {

      $response = json_encode($this->_json);
      Response::json_headers(0, strlen($response));
      print $response;
    }
  }

  public function __toString(): string {

    $this->dumpOnDestruct = false;
    return json_encode($this->_json);
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

  public function count() {

    return count($this->_json);
  }

  /**
   *
   * add data on the 'data' key to the object
   *
   * this firms up what an api call should look like
   *
   * _brayworth_.api( url, data)
   *  .then( d => {}).catch(_.growl);
   *
   * @param string|array|object $data
   * @return $this
   */
  public function data(null|string|array|object $data): self {

    return $this->add('data', $data);  // chain
  }

  public function dump() {

    $this->dumpOnDestruct = false;
    sys::dump($this->_json);
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
