#!/usr/bin/env php
<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

include __DIR__ . '/../../../vendor/autoload.php';

$src = realpath(__DIR__ . '/bootstrap5/vendor/twbs/bootstrap/dist/js/bootstrap.bundle.js');
$minifiedPath = realpath(__DIR__ . '/../../dvc/js/bootstrap5');

if ($src) {

  if ( $minifiedPath) {

    $minifier = new \MatthiasMullie\Minify\JS($src);
    $minifier->minify($minifiedPath . '/bootstrap.bundle.min.js');
  } else {

    print 'cannot locate target path';
  }
} else {

  print 'cannot locate source';
}
