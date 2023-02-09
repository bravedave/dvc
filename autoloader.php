<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

include_once __DIR__ . '/src/dvc/utility.php';
bravedave\dvc\application::load_dvc_autoloader_fallback();

if (file_exists($vendor = __DIR__ . '/vendor/autoload.php')) {

  require_once $vendor;
} elseif (file_exists($vendor = __DIR__ . '/../../autoload.php')) {

  require_once $vendor;
}

\sys::logger(sprintf('<deprecation - not required> %s', __FILE__));
