<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/

	if ( ! defined( "APPLICATION")) exit;	?>
<style>
html, body, .content { height: 100% }
.main-content-wrapper {
	height: calc( 100% - 20px ); }
.content { margin: 0; padding-bottom: 0 }
.lost-page {
	height : calc( 100% + 30px);
	margin: -30px -15px;
	padding: 5%;
	background: url(<?php print url::$URL ?>images/lost.jpg) no-repeat center center fixed;
	-webkit-background-size: cover;
	-moz-background-size: cover;
	-o-background-size: cover;
	background-size: cover;
	position: relative
}
.lost-page .lost-message {
	background-color: white;
	border-radius: 10px;
	display: inline-block;
	padding: 2% 5% 5% 5%;
	position: absolute;
	bottom: 2%;
	left: 2%;
}
</style>
<div class="lost-page">

	<div class="lost-message">
		<h1>404</h1>

		<p>This is not the web page you are looking for</p>

	</div>

</div>
