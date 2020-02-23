<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/
class logon extends Controller {
	public $RequireValidation = false;

	public function index() {
		if ( auth::GoogleAuthEnabled())
			Response::redirect( \url::tostring( 'auth/request'));

		else
			throw new dvc\Exceptions\NoAuthenticationMethodsAvailable;	// home page

	}

}
