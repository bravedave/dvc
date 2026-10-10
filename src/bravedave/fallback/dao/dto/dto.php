<?php
/*
 * Copyright (c) 2026 David Bray
 * Licensed under the MIT License. See LICENSE file for details.
*/

namespace dao\dto;

use bravedave;
use bravedave\dvc\logger;
class dto extends bravedave\dvc\dto {

public function __construct($row = null) {

    logger::trace(sprintf('deprecated : please call bravedave\dvc\dto directly : %s', get_class($this)), 1);
    parent::__construct($row);
  }
}
