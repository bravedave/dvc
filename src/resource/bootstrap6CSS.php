<?php
/*
 * Copyright (c) 2026 David Bray
 * Licensed under the MIT License. See LICENSE file for details.
*/

include __DIR__ . '/../../vendor/autoload.php';

$src = realpath(__DIR__ . '/bootstrap6/vendor/twbs/bootstrap/dist/css/bootstrap.css');
$minifiedPath = realpath(__DIR__ . '/../bravedave/dvc/css/bootstrap6');

if ($src) {

  if ($minifiedPath) {

    $content = file_get_contents($src);
    $updatedContent = $content;

    $srcModified = $minifiedPath . '/bootstrap.css';

    // Save the modified content back to the file
    print "modifying path : $srcModified\n";
    file_put_contents($srcModified, $updatedContent);

    print "target path : $minifiedPath\n";
    $minifier = new \MatthiasMullie\Minify\CSS($srcModified);
    $minifier->minify($minifiedPath . '/bootstrap.min.css');
  } else {

    print 'cannot locate target path';
  }
} else {

  print 'cannot locate source';
}
