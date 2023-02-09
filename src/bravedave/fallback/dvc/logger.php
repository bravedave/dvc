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

namespace dvc;

use bravedave;

abstract class logger extends bravedave\dvc\logger {
}

logger::deprecated('this class is deprecated (dvc\logger) %s');
