<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

extract((array)$this->data);  ?>

<nav class="navbar navbar-expand bg-primary" data-bs-theme="dark">
  <div class="container">
    <a class="navbar-brand" href="#"><?= $title ?></a>

    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
      <li class="nav-item">
        <a class="nav-link active" aria-current="page" href="<?= strings::url() ?>">Home</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="https://github.com/bravedave" target="_blank"><i class="bi bi-github"></i></a>
      </li>
    </ul>
  </div>
</nav>
