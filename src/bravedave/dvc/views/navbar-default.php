<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * compatibility : bootstrap 5
*/

use dvc\theme;

$title = $this->data->title ?? $this->title;  ?>

<nav class="<?= theme::navbar() ?> navbar-expand" role="navigation">
  <div class="container-fluid">

    <div class="navbar-brand"><?= $title  ?></div>

    <ul class="ms-auto navbar-nav">

      <li class="nav-item">

        <a class="nav-link" href="<?= strings::url() ?>">

          <i class="bi bi-house"></i>
          <span class="d-none d-md-inline">Home</span>
        </a>
      </li>

      <li class="nav-item">

        <a class="nav-link" href="<?= strings::url('/docs/') ?>">

          <i class="bi bi-file-text"></i>
          <span class="d-none d-md-inline">docs</span>
        </a>
      </li>

      <li class="nav-item">

        <a class="nav-link" href="https://github.com/bravedave/">
          <i class="bi bi-github"></i>
          <span class="d-none d-md-inline">GitHub</span>
        </a>
      </li>
    </ul>
  </div>
</nav>
