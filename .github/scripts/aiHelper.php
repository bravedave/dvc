<?php
/*
 * Copyright (c) 2025 David Bray
 * Licensed under the MIT License. See LICENSE file for details.
 *
 * php .github/scripts/aiHelper.php [script.php]
*/

require __DIR__ . '/../../vendor/autoload.php';

class aiHelper extends service {

  static function helper(array $args) {

    $script = isset($args[1]) ? realpath($args[1]) : null;
    if (count($args) > 2 || (isset($args[1]) && (!$script || !is_file($script) || !is_readable($script)))) {
      fwrite(STDERR, "Usage: php .github/scripts/aiHelper.php [script.php]\n");
      exit(1);
    }

    config::$DB_CACHE_WARNING_ENABLED = false;

    $app = new self(application::startDir());
    $app->_execute(function () use ($script) {
      if ($script) {
        require $script;
      } else {
        echo "hello world\n";
      }
    });
  }
}

aiHelper::helper($argv);
