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

abstract class Controller extends bravedave\dvc\controller {}

logger::deprecated(sprintf('do not continue to use this class (dvc\Controller) : %s', __METHOD__));
