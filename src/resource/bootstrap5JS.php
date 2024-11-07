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

include __DIR__ . '/../../vendor/autoload.php';

$src = realpath(__DIR__ . '/bootstrap5/vendor/twbs/bootstrap/dist/js/bootstrap.bundle.js');
$minifiedPath = realpath(__DIR__ . '/../bravedave/dvc/js/bootstrap5');

if ($src) {

  if ($minifiedPath) {

    $content = file_get_contents($src);

    // Remove the line with `aria-hidden` and move it after `EVENT_HIDDEN$4`
    $updatedContent = preg_replace(
      '/\s*this._element.setAttribute\(\'aria-hidden\', true\);\n\s*/',
      '',
      $content
    );

    $updatedContent = preg_replace(
      '/(EventHandler.trigger\(this._element, EVENT_HIDDEN\$4\);)/',
      "$1\n        this._element.setAttribute('aria-hidden', true);",
      $updatedContent
    );

    $srcModified = $minifiedPath . '/bootstrap.bundle.js';

    // Save the modified content back to the file
    print "modifying path : $srcModified\n";
    file_put_contents($srcModified, $updatedContent);

    print "target path : $minifiedPath\n";
    // $minifier = new \MatthiasMullie\Minify\JS($src);
    $minifier = new \MatthiasMullie\Minify\JS($srcModified);
    $minifier->minify($minifiedPath . '/bootstrap.bundle.min.js');
  } else {

    print 'cannot locate target path';
  }
} else {

  print 'cannot locate source';
}
