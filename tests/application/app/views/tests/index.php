<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/	?>

<ul class="list-unstyled mt-4">
<?php if ( $this->Request->ServerIsLocal()) { ?>
	<li><a href="<?= strings::url('tests/info') ?>">phpinfo()</a>

<?php } // if ( Request::ServerIsLocal()) ?>

	<li><a href="<?= strings::url( 'tests/errtest') ?>">Throw an error</a></li>

	<li><a href="<?= strings::url('dashboard') ?>">Dashboard</a></li>

<?php if ( 'hello' != $this->name) { ?>
	<li class="pt-2"><a href="<?= strings::url('hello') ?>">Hello World</a></li>

<?php }	// if ( 'hello' != $this->name)  ?>

</ul>
<br />
<br />
<br />
<br />
