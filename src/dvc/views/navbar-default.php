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

<nav class="<?= theme::navbar() ?>" role="navigation">
  <div class="container-fluid">
    <div class="navbar-brand"><?= $this->data->title  ?></div>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#<?= $_uid = strings::rand() ?>" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="<?= $_uid ?>">
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
  </div>

</nav>
