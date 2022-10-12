<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dao;

use dvc;

class bwui extends dvc\dao\bwui {}

\sys::logger( sprintf('<%s> %s', 'deprecated use dvc\dao\bwui', __METHOD__));
