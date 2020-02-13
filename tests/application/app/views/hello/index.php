<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * This work is licensed under a Creative Commons Attribution 4.0 International Public License.
 *      http://creativecommons.org/licenses/by/4.0/
 *
*/	?>
<br />
<br />
<br />
<ul class="list-unstyled mt-4">
	<li><h6><a class="text-secondary" href="<?= strings::url('hello') ?>">Hello</a></h6></li>
<?php if ( $this->Request->ServerIsLocal()) { ?>
	<li><a href="<?= strings::url('hello/info') ?>">View phpinfo()</a>

<?php } // if ( Request::ServerIsLocal()) ?>

	<li><a href="<?= strings::url( 'tests/errtest') ?>">Throw an error</a></li>

</ul>
<br />
<br />
<br />
<br />
