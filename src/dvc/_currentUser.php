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

class _currentUser extends core\currentUser {}

\sys::logger( sprintf('deprecated : do not continue to use this class (dvc\_currentUser) : %s', __METHOD__));
