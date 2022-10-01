<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc\controller;

use auth, Controller, dvc, Response;
use strings;

class logon extends Controller {
  public $RequireValidation = false;

  public function form() {
    $this->load('logon');
  }

  public function index() {
    if (auth::GoogleAuthEnabled()) {
      Response::redirect(strings::url('auth/request'));
    } else {
      throw new dvc\Exceptions\NoAuthenticationMethodsAvailable;  // home page

    }
  }
}
