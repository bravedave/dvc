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

class fileUploader {
  public $path = '';  // path to save files to

  public $accept = [];  // array of acceptable file types

  public function __construct(array $_params = []) {
    $options = array_merge([
      'path' => config::dataPath(),
      'accept' => []

    ], $_params);

    $this->path = rtrim($options['path'], '/ ');
    $this->accept = $options['accept'];
  }

  public function save(array $file, string $fileName = '', array $deleteFiles = []) {
    $debug = false;
    // $debug = true;

    /*--- ---[uploads]--- ---*/
    if ($debug) \sys::logger(sprintf('%s : %s', $file['name'], __METHOD__));

    if (is_uploaded_file($file['tmp_name'])) {
      $strType = mime_content_type($file['tmp_name']);
      if ($debug) \sys::logger(sprintf('%s (%s) : %s', $file['name'], $strType, __METHOD__));

      if (!$this->accept || in_array($strType, $this->accept)) {
        $source = $file['tmp_name'];
        $target = implode(DIRECTORY_SEPARATOR, [
          $this->path,
          strtolower($fileName ?: $file['name'])

        ]);

        if (in_array($strType, ['image/jpeg', 'image/pjpeg'])) {
          if (!preg_match('@\.jpe?g$@', $target)) {
            $target .= '.jpg';
          }
        } elseif (in_array($strType, ['image/png', 'image/x-png'])) {
          if (!preg_match('@\.png$@', $target)) {
            $target .= '.png';
          }
        } elseif (in_array($strType, ['application/pdf'])) {
          if (!preg_match('@\.pdf$@', $target)) {
            $target .= '.pdf';
          }
        } elseif (in_array($strType, ['text/csv'])) {
          if (!preg_match('@\.csv$@', $target)) {
            $target .= '.csv';
          }
        }

        if ($debug) \sys::logger(sprintf('<target %s> %s', $target, __METHOD__));

        foreach ($deleteFiles as $delete) {
          $_dtarget = implode(DIRECTORY_SEPARATOR, [
            $this->path,
            $delete

          ]);
          if ($debug) \sys::logger(sprintf('<delete %s> %s', $_dtarget, __METHOD__));
          if (file_exists($_dtarget)) unlink($_dtarget);
        }

        if (file_exists($target)) unlink($target);
        if (move_uploaded_file($source, $target)) {
          chmod($target, 0666);
          return true;
        } else {
          \sys::logger(sprintf('%s error moving file : %s', $file['name'], __METHOD__));
          return false;
        }
      } elseif (!$strType) {
        \sys::logger(sprintf('%s invalid file type : %s', $file['name'], __METHOD__));
        return false;
      } else {
        \sys::logger(sprintf('%s invalid file type - %s : %s', $file['name'], $strType, __METHOD__));
        return false;
      }
    } elseif (UPLOAD_ERR_INI_SIZE == $file['error']) {
      \sys::logger(sprintf('%s size exceeds ini size', $file['name'], __METHOD__));
      return false;
    } else {
      \sys::logger(sprintf('is not an uploaded file ? : %s : %s', $file['name'], __METHOD__));
      return false;
    }
  }
}
