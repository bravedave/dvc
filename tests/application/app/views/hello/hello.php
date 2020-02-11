<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/	?>
<h1>hello world</h1>
<?php if ( $this->Request->ServerIsLocal()) { ?>
<ul>
  <li><a href="<?php url::write('hello/info') ?>">View phpinfo()</a>

  </li>

</ul>
<?php } // if ( Request::ServerIsLocal()) ?>
