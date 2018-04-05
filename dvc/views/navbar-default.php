<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	DO NOT change this file
	Copy it to <application>/app/views/ and modify it there
	*/	?>
	<nav class="navbar navbar-light bg-light sticky-top" role="navigation" >
		<div class="navbar-header" >
			<?php printf( '<a href="%s" class="navbar-brand" >%s</a>', \url::$URL, $this->data->title);	?>

		</div>

		<a style="position: absolute; top: 0; right: 0; border: 0; display: block; z-index: 1050;"
			href="https://github.com/bravedave/">
			<img src="<?php url::write( 'images/forkme_right_green.png') ?>" alt="Fork me on GitHub" />

		</a>

	</nav>
