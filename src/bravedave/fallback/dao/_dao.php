<?php
/*
 * Copyright (c) 2026 David Bray
 * Licensed under the MIT License. See LICENSE file for details.
*/

namespace dao;

use bravedave;
use bravedave\dvc\logger;

abstract class _dao extends bravedave\dvc\dao {

  public function __construct(?bravedave\dvc\db $db = null) {

    logger::trace(sprintf('deprecated : please call dvc\dao\_dao directly : %s', get_class($this)), 2);
    parent::__construct($db);
  }
}
