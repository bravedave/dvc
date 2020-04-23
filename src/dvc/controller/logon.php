<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

class logon extends Controller {
	public $RequireValidation = false;

	public function index() {
		if ( auth::GoogleAuthEnabled()) {
			Response::redirect( strings::url( 'auth/request'));

		}
		else {
			throw new dvc\Exceptions\NoAuthenticationMethodsAvailable;	// home page

		}

	}

}
