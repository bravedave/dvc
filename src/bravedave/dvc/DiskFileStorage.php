<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * Example usage:
 * $storage = new DiskFileStorage('/path/to/storage');
 * $storage->storeFile($_FILES['file'], 'example.txt');
 *
 * $fileContent = $storage->getFile('example.txt');
 * if ($fileContent !== null) {
 *   // Process the file content
 *   echo $fileContent;
 * }
 *
 * $storage->deleteFile('example.txt');
*/

namespace bravedave\dvc;

use RuntimeException;

class DiskFileStorage {
  protected string $storagePath;

  protected function _filepath(string $filename): string {

    return implode(
      DIRECTORY_SEPARATOR,
      [
        $this->storagePath,
        $this->_safe_file_name($filename)
      ]
    );
  }

  protected function _safe_file_name(string $filename): string {

    $filename = mb_ereg_replace("([^a-zA-Z0-9\s\.\-\_])", '_', $filename);
    $filename = mb_ereg_replace("([_]{2,})", '_', $filename);
    // Remove any runs of periods (thanks falstro!)
    $filename = mb_ereg_replace("([\.]{2,})", '', $filename);
    // logger::info( sprintf('<%s> %s', $filename, __METHOD__));
    return $filename;

    // https://stackoverflow.com/questions/2021624/string-sanitizer-for-filename

    // Remove anything which isn't a word, whitespace, number
    // or any of the following caracters -_~,;[]().
    // If you don't need to handle multi-byte characters
    // you can use preg_replace rather than mb_ereg_replace
    // Thanks @Łukasz Rysiak!
    $file = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $filename);
    // Remove any runs of periods (thanks falstro!)
    $file = mb_ereg_replace("([\.]{2,})", '', $file);

    return $file;
  }

  public function __construct($storagePath) {

    $this->storagePath = rtrim(rtrim($storagePath), '/');
    if (!$this->storagePath) throw new RuntimeException('Invalid storage path');
  }

  public function deleteFile($fileName): void {

    $filePath = $this->_filepath($fileName);

    if (file_exists($filePath)) {

      // logger::debug(sprintf('<delete %s> %s', $fileName, __METHOD__));
      unlink($filePath);
    }
  }

  public function file_exists($fileName): bool {

    return file_exists($this->_filepath($fileName));
  }

  public function getFile(string $fileName): ?string {

    $filePath = $this->_filepath($fileName);
    if (file_exists($filePath)) {

      return file_get_contents($filePath);
    } else {

      return null;
    }
  }

  public function getPath(string $fileName = ''): string {

    if (!$fileName) return $this->_filepath($fileName);
    return $this->storagePath;
  }

  public function isValid(): bool {

    if ($this->storagePath) return is_dir($this->storagePath);
    return false;
  }

  public function mime_type($fileName): string {

    return mime_content_type($this->_filepath($fileName));
  }

  public function modified(string $fileName = ''): int {

    if (!$this->isValid()) return 0;
    if (!$fileName) return filemtime($this->storagePath);
    return filemtime($this->_filepath($fileName));
  }

  public function serve(string $filename): void {

    Response::serve($this->_filepath($filename));
  }

  public function subFolder(string $path, bool $create = true): static {

    $folder = $this->_filepath($path);
    if (!is_dir($folder) && $create) mkdir($folder, 0777, true);
    return new static($folder);
  }

  public function storeFile($file, $fileName = ''): string {

    if (!$fileName) $fileName = $file['name'] ?? '';

    if ($fileName) {

      if (is_uploaded_file($file['tmp_name'])) {

        $targetPath = $this->_filepath($fileName);
        if (file_exists($targetPath)) unlink($targetPath);
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {

          return $targetPath;
        }
      }
    }
    return '';
  }

  public function touch(string $fileName): void {

    $path = $this->_filepath($fileName);
    touch($path);
    @chmod($path, 0666);
  }
}
