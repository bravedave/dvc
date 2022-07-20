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

use strings;

class EmailAddress {
  public $email,
    $name;

  public function __construct(string $el) {
    if (strpos($el, '<') !== false) {
      $el = trim($el, '> ');
      $a = explode("<", $el);

      /* remove quote enclosures */
      $this->name = preg_replace(
        [
          '/^("|\')/',
          '/("|\')$/'
        ],
        '',
        trim($a[0])
      );

      $this->email = trim($a[1]);
    } else {
      $this->name = '';
      $this->email = trim($el);
    }
  }

  public function check() {
    return strings::CheckEmailAddress($this->email);
  }

  public function rfc822() {

    if ($this->name) return strings::rfc822($this->email, $this->name);

    return strings::rfc822($this->email);
  }
}
