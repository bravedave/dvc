<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/?>
<style>
nav ul a,
nav .brand-logo {
	color: #444;
}
.button-collapse {
	color: #26a69a;
}
</style>
<nav class="white" role="navigation">
	<div class="nav-wrapper container">
		<a id="logo-container" href="#" class="brand-logo">Logo</a>
		<ul class="right hide-on-med-and-down">
			<li><a href="#">Navbar Link</a></li>
		</ul>

		<ul id="nav-mobile" class="side-nav">
			<li><a href="#">Navbar Link</a></li>
		</ul>
		<a href="#" data-activates="nav-mobile" class="button-collapse"><i class="material-icons">menu</i></a>
	</div>
</nav>
<script>
(function($){
	$(function(){
		$('.button-collapse').sideNav();

	}); // end of document ready

})(jQuery); // end of jQuery name space
</script>

