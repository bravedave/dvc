<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 * 
 * MIT License
 *
*/

namespace example;

use bravedave\dvc\{
    controller as dvcController,
    ServerRequest
};

class controller extends dvcController {

    protected function _index() {

        $this->data = (object)[
            'title' => $this->title = config::label,
        ];

        $this->renderBS5([
            'aside' => fn() => $this->load('blank'),
            'main' => fn() => $this->load('index')
        ]);
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
