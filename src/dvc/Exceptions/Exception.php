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

class Exception extends \Exception {
  protected $_text = false;

  public function __construct($message = null, $code = 0, \Exception $previous = null) {

    if (!$this->_text) $this->_text = '';

    $sContext = str_replace('\\', '-', get_class($this)) . ' (' . basename($this->getFile()) . ' ~ ' . $this->getLine() . ')';
    if ($this->_text == '') {

      $this->_text = $sContext;
    } else {

      $this->_text = $sContext . ' :: ' . $this->_text;
    }

    if ($message) $this->_text .= ' : ' . (string)$message;

    // make sure everything is assigned properly
    parent::__construct($this->_text, $code, $previous);
  }
}
