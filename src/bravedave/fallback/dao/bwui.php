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

use bravedave\dvc\logger;
use dvc;

class bwui extends dvc\dao\bwui {}

logger::info( sprintf('<%s> %s', 'deprecated use dvc\dao\bwui', logger::caller()));
