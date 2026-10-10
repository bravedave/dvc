<?php
/*
 * Copyright (c) 2026 David Bray
 * Licensed under the MIT License. See LICENSE file for details.
*/

namespace example;    ?>

<h1>Hello World</h1>

<?php (new \bravedave\dvc\view((object)['name' => 'example'], [__DIR__ . '/']))('hello-world'); ?>