<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc\Exceptions;

use bravedave\dvc\Exceptions\Exception;

class DatapathNotWritable extends Exception {
  protected $_text = 'error/datapath not writable';

  public function __construct($message = null, $code = 0, \Exception $previous = null) {

    $this->_text = implode('<br>', [
      $this->_text,
      sprintf('please create a writable data folder : %s', $message),
      sprintf('mkdir --mode=0777 %s', $message),
    ]);

    // make sure everything is assigned properly
    parent::__construct($this->_text, $code, $previous);
  }
}
