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

class _currentUser extends bravedave\dvc\currentUser {}

\sys::logger( sprintf('deprecated : do not continue to use this class (dvc\_currentUser) : %s', __METHOD__));
