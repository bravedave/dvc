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

abstract class auth extends bravedave\dvc\auth {}

logger::deprecated(sprintf('do not continue to use this class (dvc\auth) : %s', __METHOD__));
