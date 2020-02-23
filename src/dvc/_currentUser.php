<?php
/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * This work is licensed under a Creative Commons Attribution 4.0 International Public License.
 *      http://creativecommons.org/licenses/by/4.0/
 *
*/

namespace dvc;

class _currentUser extends core\currentUser {}

\sys::logger( sprintf('deprecated : do not continue to use this class (dvc\_currentUser) : %s', __METHOD__));
