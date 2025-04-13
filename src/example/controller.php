<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 * 
 * MIT License
 *
*/

namespace example\controller;

use bravedave\dvc\{
    controller as dvcController,
    ServerRequest
};

class controller extends dvcController {

    protected function _index() {
    }

    protected function before() {
        parent::before();
        $this->viewPath[] = __DIR__ . '/views/';
    }

    protected function postHandler() {
        $request = (new ServerRequest);
        $action = $request('action');
        return match ($action) {
            default => parent::postHandler()
        };
    }
}
