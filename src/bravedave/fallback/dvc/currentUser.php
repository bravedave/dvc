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

abstract class currentUser extends bravedave\dvc\currentUser {
}

logger::deprecated(sprintf('do not continue to use this class (dvc\currentUser) : %s', __METHOD__));
