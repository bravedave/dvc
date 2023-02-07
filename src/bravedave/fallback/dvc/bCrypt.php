<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc;

use bravedave;

abstract class bCrypt extends bravedave\dvc\bCrypt {
}

logger::deprecated(sprintf('do not continue to use this class (dvc\bCrypt) : %s', __METHOD__));
