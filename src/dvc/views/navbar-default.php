<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
**/	?>

<nav class="navbar navbar-expand navbar-light bg-light sticky-top" role="navigation" >
	<div class="container-fluid">
    <div class="navbar-brand" ><?= $this->data->title	?></div>

    <ul class="ml-auto navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="<?= strings::url() ?>">
          <i class="bi bi-house"></i>

        </a>

      </li>

      <li class="nav-item">
        <a class="nav-link" href="https://github.com/bravedave/">
          <i class="bi bi-github"></i>

        </a>

      </li>

    </ul>

  </div>

</nav>
