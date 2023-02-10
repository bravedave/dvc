<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * DO NOT change this file
 * Copy it to <application>/app/dvc/ and modify it there
*/

namespace dvc;

use bravedave;

class timer extends bravedave\dvc\timer {
}

logger::deprecated(sprintf('do not continue to use this class (dvc\timer) : %s', __METHOD__));
