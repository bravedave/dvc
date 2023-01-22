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

class _user extends bravedave\dvc\user {}

logger::deprecated(sprintf('do not continue to use this class (dvc\_user) : %s', __METHOD__));
