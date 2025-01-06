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

use config, strings;
use Imagick;
use Nyholm\Psr7\UploadedFile;
use SplFileInfo;

class fileUploader {
  public $path = '';  // path to save files to
  public $convertHEICtoJPEG = false;

  public $accept = [];  // array of acceptable file types

  public function __construct(array $_params = []) {

    $options = array_merge([
      'path' => config::dataPath(),
      'accept' => []
    ], $_params);

    $this->path = rtrim($options['path'], '/ ');
    $this->accept = $options['accept'];
  }

  protected function _save(array $file, string $fileName = '', array $deleteFiles = []): bool {
    $debug = false;
    // $debug = true;

    /*--- ---[uploads]--- ---*/
    if ($debug) logger::debug(sprintf('<%s> %s', $file['name'], logger::caller()));

    if (is_uploaded_file($file['tmp_name'])) {

      $strType = mime_content_type($file['tmp_name']);
      if ($debug) logger::debug(sprintf('<%s (%s)> %s', $file['name'], $strType, logger::caller()));

      if (!$this->accept || in_array($strType, $this->accept)) {

        $source = $file['tmp_name'];
        $target = implode(DIRECTORY_SEPARATOR, [
          $this->path,
          strings::safe_file_name(strtolower('' == (string)$fileName ? $file['name'] : $fileName))
        ]);

        if (in_array($strType, ['image/heic', 'image/heif'])) {

          if (!preg_match('@\.heic$@', $target)) $target .= '.heic';
        } elseif (in_array($strType, ['image/jpeg', 'image/pjpeg'])) {

          if (!preg_match('@\.jpe?g$@', $target)) $target .= '.jpg';
        } elseif (in_array($strType, ['image/png', 'image/x-png'])) {

          if (!preg_match('@\.png$@', $target)) $target .= '.png';
        } elseif (in_array($strType, ['application/pdf'])) {

          if (!preg_match('@\.pdf$@', $target)) $target .= '.pdf';
        } elseif (in_array($strType, ['text/csv'])) {

          if (!preg_match('@\.csv$@', $target)) $target .= '.csv';
        }

        if ($debug) logger::debug(sprintf('<target %s> %s', $target, logger::caller()));

        foreach ($deleteFiles as $delete) {

          $_dtarget = implode(DIRECTORY_SEPARATOR, [
            $this->path,
            $delete
          ]);

          if ($debug) logger::debug(sprintf('<delete %s> %s', $_dtarget, logger::caller()));
          if (file_exists($_dtarget)) unlink($_dtarget);
        }

        if (file_exists($target)) unlink($target);
        if (move_uploaded_file($source, $target)) {

          if ($this->convertHEICtoJPEG) {

            if (in_array($strType, ['image/heic', 'image/heif'])) {

              $spl = new SplFileInfo($target);
              if ('heic' == strtolower($spl->getExtension())) {

                if ($debug) logger::debug(sprintf('<%s> %s', 'heic file, converting', __METHOD__));
                $imagick = new Imagick;
                $target = preg_replace('@\.heic$@i', '.jpg', $spl->getPathname());
                $imagick->readImage($spl->getPathname());
                $imagick->writeImage($target);

                unlink($spl->getPathname());
              }
            }
          }
          return true;
        }

        logger::info(sprintf('<%s error moving file> %s', $file['name'], logger::caller()));
        return false;
      } elseif (!$strType) {

        logger::info(sprintf('<%s invalid file type> %s', $file['name'], logger::caller()));
        return false;
      } else {

        logger::info(sprintf('<%s invalid file type - %s> %s', $file['name'], $strType, logger::caller()));
        return false;
      }
    } elseif (UPLOAD_ERR_INI_SIZE == $file['error']) {

      logger::info(sprintf('<%s size exceeds ini size> %s', $file['name'], logger::caller()));
      return false;
    } else {

      logger::info(sprintf('<is not an uploaded file ? : %s> %s', $file['name'], logger::caller([__METHOD__])));
      return false;
    }
  }

  protected function _savePSR7(UploadedFile $file, string $fileName = '', array $deleteFiles = []): bool {
    $debug = false;
    // $debug = true;

    /*--- ---[uploads]--- ---*/
    if ($debug) logger::debug(sprintf('<%s> %s', $file->getClientFilename(), logger::caller()));

    $mimeType = $file->getClientMediaType();
    if ($debug) logger::debug(sprintf('<%s (%s)> %s', $file->getClientFilename(), $mimeType, logger::caller()));

    if (!$this->accept || in_array($mimeType, $this->accept)) {

      $target = implode(DIRECTORY_SEPARATOR, [
        $this->path,
        strings::safe_file_name(strtolower('' == (string)$fileName ? $file->getClientFilename() : $fileName))
      ]);

      if (in_array($mimeType, ['image/heic', 'image/heif'])) {

        if (!preg_match('@\.heic$@', $target)) $target .= '.heic';
      } elseif (in_array($mimeType, ['image/jpeg', 'image/pjpeg'])) {

        if (!preg_match('@\.jpe?g$@', $target)) $target .= '.jpg';
      } elseif (in_array($mimeType, ['image/png', 'image/x-png'])) {

        if (!preg_match('@\.png$@', $target)) $target .= '.png';
      } elseif (in_array($mimeType, ['application/pdf'])) {

        if (!preg_match('@\.pdf$@', $target)) $target .= '.pdf';
      } elseif (in_array($mimeType, ['text/csv'])) {

        if (!preg_match('@\.csv$@', $target)) $target .= '.csv';
      }

      if ($debug) logger::debug(sprintf('<target %s> %s', $target, logger::caller()));

      foreach ($deleteFiles as $delete) {

        $_dtarget = implode(DIRECTORY_SEPARATOR, [
          $this->path,
          $delete
        ]);

        if ($debug) logger::debug(sprintf('<delete %s> %s', $_dtarget, logger::caller()));
        if (file_exists($_dtarget)) unlink($_dtarget);
      }

      // Move the uploaded file to the destination folder
      try {

        if (file_exists($target)) unlink($target);
        if ($file->moveTo($target)) {

          if ($this->convertHEICtoJPEG) {

            if (in_array($mimeType, ['image/heic', 'image/heif'])) {

              $spl = new SplFileInfo($target);
              if ('heic' == strtolower($spl->getExtension())) {

                if ($debug) logger::debug(sprintf('<%s> %s', 'heic file, converting', __METHOD__));
                $imagick = new Imagick;
                $target = preg_replace('@\.heic$@i', '.jpg', $spl->getPathname());
                $imagick->readImage($spl->getPathname());
                $imagick->writeImage($target);

                unlink($spl->getPathname());
              }
            }
          }
          return true;
        }
      } catch (\Exception $e) {

        logger::info(sprintf('<%s error moving file> %s', $file->getClientFilename(), logger::caller()));
        return false;
      }
    } elseif (!$mimeType) {

      logger::info(sprintf('<%s invalid file type> %s', $file->getClientFilename(), logger::caller()));
      return false;
    } else {

      logger::info(sprintf('<%s invalid file type - %s> %s', $file->getClientFilename(), $mimeType, logger::caller()));
      return false;
    }
  }

  public function save(array|UploadedFile $file, string $fileName = '', array $deleteFiles = []): bool {

    if ($file instanceof UploadedFile) {

      return $this->_savePSR7($file, $fileName, $deleteFiles);
    } else {

      return $this->_save($file, $fileName, $deleteFiles);
    }
  }
}
