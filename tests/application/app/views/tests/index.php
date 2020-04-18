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
	<li><a href="<?= strings::url( 'tests/changes') ?>">Changes</a></li>
	<li><a href="#" id="<?= $uid = strings::rand() ?>">Modal Dialog</a></li>
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

	<li><a href="#" id="<?= $uid = strings::rand() ?>">Ask a Question</a></li>
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

	<li><a href="<?= strings::url('dashboard') ?>">Dashboard</a></li>

<?php if ( 'hello' != $this->name) { ?>
	<li class="pt-2"><a href="<?= strings::url('hello') ?>">Hello World</a></li>

<?php }	// if ( 'hello' != $this->name)  ?>

</ul>
<br />
<br />
<br />
<br />
