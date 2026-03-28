<?php
/*
 * Copyright (c) 2026 David Bray
 * Licensed under the MIT License. See LICENSE file for details.
*/

namespace bravedave\dvc\controller;

use bravedave\dvc\logger;
use bravedave\dvc\Request;
use bravedave\dvc\Response;
use Controller;

class auth extends Controller {

  protected $RequireValidation = false;

  protected function _index() {

    Response::redirect();
  }

  protected function before() {

    logger::info(sprintf('<honeytrap %s> %s', Request::get()->getRemoteIP(), logger::caller()));
    parent::before();
  }

  protected function postHandler() {
  }
}
