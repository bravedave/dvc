<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/	?>

<form id="<?= $_form = strings::rand() ?>" action="<?= strings::url('install/') ?>" method="POST">
	<input type="hidden" name="action" value="create-database" />

	<div class="form-group row"><!-- type -->
		<label class="col-form-label col-sm-3" for="<?= $_uid = strings::rand() ?>">Type</label>
		<div class="col-sm-9">
			<select class="form-control" name="db_type" id="<?= $_uid ?>">
				<option value="sqlite" selected>SQLite</option>
				<option value="mysql">MariaDB/MySQL</option>

			</select>

		</div>
		<script>
		$(document).ready( function() {
			$('#<?= $_uid ?>')
			.on( 'change', function( e) {
				let _me = $(this);
				if ( 'sqlite' == _me.val()) {
					$('#<?= $_form ?>').trigger('format-sqlite');

				}
				else {
					$('#<?= $_form ?>').trigger('format-mysql');

				}

			});

		});
		</script>

	</div>

	<div class="form-group d-none row"><!-- host -->
		<label class="col-form-label col-sm-3" for="<?= $_uid = strings::rand() ?>">Host</label>
		<div class="col-sm-9">
			<input class="form-control" type="text" name="db_host" id="<?= $_uid ?>" value="localhost" />

		</div>

	</div>

	<div class="form-group d-none row"><!-- root password -->
		<label class="col-form-label col-sm-3" for="<?= $_uid = strings::rand() ?>">Root Password</label>
		<div class="col-sm-9">
			<input class="form-control" type="password" name="root_password" id="<?= $_uid ?>" />

		</div>

	</div>

	<div class="form-group d-none row"><!-- db name -->
		<label class="col-form-label col-sm-3" for="<?= $_uid = strings::rand() ?>">Database Name</label>
		<div class="col-sm-9">
			<input class="form-control" type="text" name="db_name" id="<?= $_uid ?>" />

		</div>

	</div>

	<div class="form-group d-none row"><!-- db user -->
		<label class="col-form-label col-sm-3" for="<?= $_uid = strings::rand() ?>">Database User</label>
		<div class="col-sm-9">
			<input class="form-control " type="text" name="db_user" id="<?= $_uid ?>" />

		</div>

	</div>

	<div class="form-group d-none row"><!-- db password -->
		<label class="col-form-label col-sm-3" for="<?= $_uid = strings::rand() ?>">Database Password</label>
		<div class="col-sm-9">
			<input class="form-control " type="text" name="db_pass" id="<?= $_uid ?>" />

		</div>

	</div>

	<div class="form-group text-right">
		<button class="btn btn-outline-primary" type="submit">submit</button>

	</div>

</form>
<script>
$(document).ready( function() {
	let _form = $('#<?= $_form ?>');

	let _fields = [
		$('input[name="db_host"]', _form),
		$('input[name="db_name"]', _form),
		$('input[name="db_user"]', _form),
		$('input[name="db_pass"]', _form),

	];

	_form
	.on( 'format-sqlite', function(e) {
		$.each( _fields, (i,fld) => {
			fld.prop('required', false);
			fld.closest('.form-group').addClass('d-none');
			$('input[name="root_password"]', _form).closest('.form-group').addClass('d-none');

		});

	})
	.on( 'format-mysql', function(e) {
		$.each( _fields, (i,fld) => {
			fld.prop('required', true);
			fld.closest('.form-group').removeClass('d-none');
			$('input[name="root_password"]', _form).closest('.form-group').removeClass('d-none');

		});

	});

});
</script>