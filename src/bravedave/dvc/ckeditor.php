<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 * 
 * MIT License
 *
*/

namespace bravedave\dvc;

final class ckeditor {

  public static function serve($file = '', $translation = ''): bool {

    $basePath = __DIR__ . '/resources/ckeditor/ckeditor5/';

    if ($file) {

      $file = (string)$file;

      // Serve translation files
      if ($file === 'translations' && $translation) {
        $translationsPath = $basePath . 'translations/';
        $filename = $translation . '.js';
        $filepath = $translationsPath . $filename;

        if (is_file($filepath)) {

          Response::serve($filepath);
          return true;
        }
      }

      // Serve JS, CSS, or MAP files
      $allowedExtensions = ['js', 'css', 'map'];
      foreach ($allowedExtensions as $ext) {
        $filename = $file;
        if (pathinfo($filename, PATHINFO_EXTENSION) !== $ext) {
          $filename = $file . '.' . $ext;
        }
        $filepath = $basePath . $filename;
        if (is_file($filepath)) {

          Response::serve($filepath);
          return true;
        }
      }
    }

    return false;
  }
}
