<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\dvc\controller;

use auth, Controller, dvc, Response;
use strings;
use user;

class logon extends Controller {
  protected $RequireValidation = false;

  public function form() {
    user::$checkBWUI = false;

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
