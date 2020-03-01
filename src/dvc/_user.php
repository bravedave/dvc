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

class _user extends core\user {}

\sys::logger( sprintf('deprecated : do not continue to use this class (dvc\_user) : %s', __METHOD__));
