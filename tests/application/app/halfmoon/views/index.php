<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * styleguide : https://codeguide.co/
*/

namespace halfmoon;

use strings;  ?>

<ul class="nav flex-column text-secondary">
	<li class="nav-item h6"><a class="nav-link" href="<?= strings::url( $this->route) ?>"><?= $this->title ?></a></li>

</ul>
