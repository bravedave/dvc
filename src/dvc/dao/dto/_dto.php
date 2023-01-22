<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

// #[AllowDynamicProperties]
namespace dvc\dao\dto;

use bravedave;
use bravedave\dvc\logger;

class _dto extends bravedave\dvc\dto {}

logger::deprecated(sprintf('do not continue to use this class (dvc\dao\dto\_dto) : %s', __METHOD__));
