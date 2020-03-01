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

class _auth extends core\auth {}

\sys::logger( sprintf('deprecated : do not continue to use this class (dvc\_auth) : %s', __METHOD__));
