<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 *  DO NOT change this file
 *  Copy it to <application>/app/dvc/ and modify it there
*/

class application extends dvc\application {
    public function startDir() {
        return realpath( __DIR__ . '/../');

    }

}
