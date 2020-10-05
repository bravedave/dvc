<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/	?>
<style>
a { color: inherit; }

</style>
<ul class="nav flex-column">
	<li class="nav-item h6"><a class="nav-link" href="<?= strings::url('tests/') ?>">Tests</a></li>

<?php if ( $this->Request->ServerIsLocal()) { ?>
	<li class="nav-item"><a class="nav-link" href="<?= strings::url('tests/info') ?>">phpinfo()</a>

<?php } // if ( Request::ServerIsLocal()) ?>

	<li class="nav-item"><a class="nav-link" href="<?= strings::url( 'tests/errtest') ?>">Throw an error</a></li>
	<li class="nav-item"><a class="nav-link" href="<?= strings::url( 'tests/changes') ?>">Changes</a></li>
	<li class="nav-item"><a class="nav-link" href="<?= strings::url( 'tests/phonenumbers') ?>">Phone Numbers</a></li>
	<li class="nav-item"><a class="nav-link" href="#" id="<?= $uid = strings::rand() ?>">Modal Dialog</a></li>
	<script>
	$(document).ready( function() {
		$('#<?= $uid ?>').on( 'click', function( e) {
			e.stopPropagation();e.preventDefault();

			_brayworth_.modal({
				title : 'fred',
				text : 'hey jude'

			});

		});


	});
	</script>

	<li class="nav-item"><a class="nav-link" href="#" id="<?= $uid = strings::rand() ?>">Ask a Question</a></li>
	<script>
	$(document).ready( function() {
		$('#<?= $uid ?>').on( 'click', function( e) {
			e.stopPropagation();e.preventDefault();

			_brayworth_.ask({
				headClass: 'text-white bg-danger',
				title : 'This is Red',
				text : 'Do you agree ?',
				buttons : {
					yes : function() {
						$(this).modal('hide');
						console.log( 'ok', this);

					}

				}

			});

		});

	});
	</script>

	<?php
	if ( $this->Request->ServerIsLocal()) {
		printf( '<li class="nav-item"><a class="nav-link" href="%s">SiteMap</a></li>', strings::url( 'sitemap/report'));

	} ?>

</ul>
