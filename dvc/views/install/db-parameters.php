<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/
  if ( ! defined( "APPLICATION")) exit; ?>
<form action="<?php print url::tostring('install/') ?>" method="POST">
	<fieldset>
		<legend>DB Parameters</legend>

		<div class="form-group row">
			<label class="control-label col-sm-3" for="db_host">MySQL Host</label>
			<div class="col-sm-9">
				<input class="form-control " type="text" name="db_host" id="db_host" value="localhost" autofocus />

			</div>

		</div>

		<div class="form-group row">
			<label class="control-label col-sm-3" for="root_password">MySQL Root Password</label>
			<div class="col-sm-9">
				<input class="form-control " type="password" name="root_password" id="root_password" />

			</div>

		</div>

		<div class="form-group row">
			<label class="control-label col-sm-3" for="db_name">Database Name</label>
			<div class="col-sm-9">
				<input class="form-control " type="text" name="db_name" id="db_name" />

			</div>

		</div>

		<div class="form-group row">
			<label class="control-label col-sm-3" for="db_user">Database User</label>
			<div class="col-sm-9">
				<input class="form-control " type="text" name="db_user" id="db_user" />

			</div>

		</div>

		<div class="form-group row">
			<label class="control-label col-sm-3" for="db_pass">Database Password</label>
			<div class="col-sm-9">
				<input class="form-control " type="text" name="db_pass" id="db_pass" />

			</div>

		</div>

		<div class="form-group text-right">
			<input class="btn btn-default" name="form_action" id="form_action" type="submit" value="Submit" />

		</div>

	</fieldset>

</form>
