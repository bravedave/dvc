<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/

	$Title = 'Page Title';
	$Content = 'Page content goes here.';
	$Footer = 'Page Footer';

	if ( isset( $this->data)) {
		if ( isset( $this->data->title))
			$Title = (string)$this->data->title;
		if ( isset( $this->data->content))
			$Content = (string)$this->data->content;
		if ( isset( $this->data->footer))
			$Footer = (string)$this->data->footer;

	}

	?>
	<div data-role="header" data-position="fixed">
		<h1><?php print $Title ?></h1>
	</div><!-- /header -->

	<div role="main" class="ui-content">
		<p><?php print $Content ?></p>
	</div><!-- /content -->

	<div data-role="footer" data-position="fixed">
		<h4><?php print $Footer ?></h4>
	</div><!-- /footer -->
