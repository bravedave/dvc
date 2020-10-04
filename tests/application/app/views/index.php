<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

use dvc\push; ?>

<ul class="nav flex-column">
	<li class="nav-item h6">
		<a class="nav-link" href="<?= strings::url('hello') ?>">Hello</a>

	</li>

	<li class="nav-item h6"><a class="nav-link" href="<?= strings::url('tests') ?>">Tests</a></li>

	<li class="nav-item h6"><a class="nav-link" href="<?= strings::url('webapp') ?>">WebApp</a></li>

  <?php if ( push::enabled()) { ?>
    <li class="nav-item h6"><a class="nav-link" href="<?= strings::url('push') ?>">Push</a></li>

  <?php } // if ( push::enabled()) ?>


</ul>
