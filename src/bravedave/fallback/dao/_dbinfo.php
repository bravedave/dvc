<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dao;

use dvc\dao\_dao;

class _dbinfo extends _dao {
  protected $_store = '';

  protected function check() {
    $this->checkDIR(__DIR__);
  }

  protected function checkDIR($dir) {
    \sys::logger(sprintf('<checking %s> %s', $dir, __METHOD__));
    if ($glob = glob($dir . '/db/*.php')) {
      foreach ($glob as $f) {
        \sys::logger(sprintf('<checking => %s> %s', $f, __METHOD__));
        include_once $f;
      }
    }
  }

  protected function db_version_file() {
    return implode(
      DIRECTORY_SEPARATOR,
      [
        $this->_store,
        'db_version.json'

      ]
    );
  }

  public function __construct(\dvc\dbi $db = null, string $store = null) {
    parent::__construct($db);
    $this->_store = rtrim(!$store ? \config::dataPath() : $store, '/ ');
  }

  public $debug = false;
  public function checkVersion(string $key, float $version): float {

    $_version = 0;
    $json = (object)[];

    if (file_exists($store = $this->db_version_file())) {
      $json = json_decode(file_get_contents($store));
      $_version = 0;
      if (isset($json->{$key})) {
        $_version = (float)$json->{$key};
      }
    }

    if ($_version < $version) {
      if ($this->debug) \sys::logger(sprintf('<%s> <needs update %s ? %s> %s', $store, $_version, $version, __METHOD__));
      $this->dump($verbose = false);

      $_version = $json->{$key} = $version;

      file_put_contents($store, json_encode($json, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
      if (posix_geteuid() == fileowner($store)) {
        chmod($store, 0666);
      }
      clearstatcache();
    } else {
      if ($this->debug) \sys::logger(sprintf('<%s> <up to date %s ? %s> %s', $store, $_version, $version, __METHOD__));
    }
    return $_version;
  }

  function dump($verbose = true) {
    $this->check();
    if ((bool)$verbose) $this->db->dump();
  }
}
