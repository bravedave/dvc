<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/?>
<style>
.parallax-container {
	min-height: 380px;
	line-height: 0;
	height: auto;
	color: rgba(255,255,255,.9);
}
.parallax-container .section { width: 100%; }

@media only screen and (max-width : 992px) {
	.parallax-container .section {
		position: absolute;
		top: 40%;
	}
	#index-banner .section { top: 10%; }
}

@media only screen and (max-width : 600px) {
	#index-banner .section { top: 0; }
}

.icon-block { padding: 0 15px; }
.icon-block .material-icons { font-size: inherit; }
p { line-height: 2rem; }
</style>

<div id="index-banner" class="parallax-container">
	<div class="section no-pad-bot">
		<div class="container">
			<br /><br />

			<h1 class="header center teal-text text-lighten-2">Parallax Template</h1>
			<div class="row center">
				<h5 class="header col s12 light">A modern responsive front-end framework based on Material Design</h5>
			</div>

			<div class="row center">
				<a href="http://materializecss.com/getting-started.html" id="download-button" class="btn-large waves-effect waves-light teal lighten-1">Get Started</a>
			</div>

			<br /><br />

		</div>

	</div>

	<div class="parallax"><img src="<?php print url::$URL ?>images/parallax-demo-background1.jpg" alt="Unsplashed background img 1"></div>

</div>


<div class="container">
	<div class="section">

		<!--   Icon Section   -->
		<div class="row">
			<div class="col s12 m4">
				<div class="icon-block">
					<h2 class="center brown-text"><i class="material-icons">flash_on</i></h2>
					<h5 class="center">Speeds up development</h5>

					<p class="light">We did most of the heavy lifting for you to provide a default stylings that incorporate our custom components. Additionally, we refined animations and transitions to provide a smoother experience for developers.</p>

				</div>

			</div>

			<div class="col s12 m4">
				<div class="icon-block">
					<h2 class="center brown-text"><i class="material-icons">group</i></h2>
					<h5 class="center">User Experience Focused</h5>

					<p class="light">By utilizing elements and principles of Material Design, we were able to create a framework that incorporates components and animations that provide more feedback to users. Additionally, a single underlying responsive system across all platforms allow for a more unified user experience.</p>

				</div>

			</div>

			<div class="col s12 m4">
				<div class="icon-block">
					<h2 class="center brown-text"><i class="material-icons">settings</i></h2>
					<h5 class="center">Easy to work with</h5>

					<p class="light">We have provided detailed documentation as well as specific code examples to help new users get started. We are also always open to feedback and can answer any questions a user may have about Materialize.</p>
				</div>

			</div>

		</div>

	</div>

</div>


<div class="parallax-container valign-wrapper">
	<div class="section no-pad-bot">
		<div class="container">
			<div class="row center">
				<h5 class="header col s12 light">A modern responsive front-end framework based on Material Design</h5>
			</div>

		</div>

	</div>

	<div class="parallax"><img src="<?php print url::$URL ?>images/parallax-demo-background2.jpg" alt="Unsplashed background img 2"></div>

</div>

<div class="container">
	<div class="section">

		<div class="row">
			<div class="col s12 center">
				<h3><i class="mdi-content-send brown-text"></i></h3>
				<h4>Contact Us</h4>
				<p class="left-align light">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam scelerisque id nunc nec volutpat. Etiam pellentesque tristique arcu, non consequat magna fermentum ac. Cras ut ultricies eros. Maecenas eros justo, ullamcorper a sapien id, viverra ultrices eros. Morbi sem neque, posuere et pretium eget, bibendum sollicitudin lacus. Aliquam eleifend sollicitudin diam, eu mattis nisl maximus sed. Nulla imperdiet semper molestie. Morbi massa odio, condimentum sed ipsum ac, gravida ultrices erat. Nullam eget dignissim mauris, non tristique erat. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae;</p>

			</div>

		</div>

	</div>

</div>


<div class="parallax-container valign-wrapper">
	<div class="section no-pad-bot">
		<div class="container">
			<div class="row center">
				<h5 class="header col s12 light">A modern responsive front-end framework based on Material Design</h5>
			</div>
		</div>
	</div>

	<div class="parallax"><img src="<?php print url::$URL ?>images/parallax-demo-background3.jpg" alt="Unsplashed background img 3"></div>

</div>

<script>
(function($){
	$(function(){
		$('.button-collapse').sideNav();
		$('.parallax').parallax();

	}); // end of document ready

})(jQuery); // end of jQuery name space
</script>
