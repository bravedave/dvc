<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 *	DO NOT change this file
 *	Copy it to <application>/app/views/ and modify it there
**/	?>
<nav class="navbar navbar-expand navbar-light bg-light sticky-top" role="navigation" >
	<div class="navbar-brand" ><?= $this->data->title	?></div>

	<ul class="ml-auto navbar-nav">
		<li class="nav-item">
			<a class="nav-link" href="<?= strings::url() ?>">
				<?= dvc\icon::get( dvc\icon::house ) ?>

			</a>

		</li>

		<li class="nav-item">
			<a class="nav-link" href="https://github.com/bravedave/">
				<?= dvc\icon::get( dvc\icon::github ) ?>

			</a>

		</li>

	</ul>

</nav>
