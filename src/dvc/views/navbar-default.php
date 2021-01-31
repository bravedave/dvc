<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
**/

use dvc\theme;
?>

<nav class="<?= theme::navbar() ?>" role="navigation" >
	<div class="container-fluid">
    <div class="navbar-brand" ><?= $this->data->title	?></div>

    <ul class="ml-auto navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="<?= strings::url() ?>">
          <i class="bi bi-house"></i>
          <span class="sr-only">Home</span>

        </a>

      </li>

      <li class="nav-item">
        <a class="nav-link" href="https://github.com/bravedave/">
          <i class="bi bi-github"></i>
          <span class="sr-only">GitHub</span>

        </a>

      </li>

    </ul>

  </div>

</nav>
