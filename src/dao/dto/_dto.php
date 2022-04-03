<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dao\dto;

use dvc;

class _dto extends dvc\dao\dto\_dto {
}

\sys::logger(sprintf('deprecated : please call dvc\dao\dto\_dto directly : %s', __METHOD__));
