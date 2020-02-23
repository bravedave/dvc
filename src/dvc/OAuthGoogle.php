<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/
namespace dvc;

class OAuthGoogle {
	private $UserEmail = '';

	public function __construct( $UserEmail) {
		$this->UserEmail = $UserEmail;

	}

	/*
	 * checks the credentials for the access token, if present; it returns that
	 * or refreshes it if expired.
	 * if the credentials file is empty, it will return the authorization url to which you must redirect too
	 * for user user authorization
	 */
	public function authenticate () {
		$client = \dvc\Google::client();
			if ( !\dvc\Google::validate( $client)) {
				throw new Exceptions\OAuthGoogleAuthenticationFailure;

			}

		if ( $creds = \dvc\session::get( 'credentials'))
			return ( json_decode( $creds, true));

		return ( FALSE);

	}

	/**
	* GetOauth64
	*
	* encode the user email related to this request along with the token in base64
	* this is used for authentication, in the phpmailer smtp class
	*
	* @return string
	*/
	public function getOauth64() {
		$client = \dvc\Google::client();
			if ( !\dvc\Google::validate( $client)) {
				throw new Exceptions\OAuthGoogleAuthenticationFailure;

			}

		$token = 	\dvc\session::get( 'access_token');
		$key = "user=" . $this->UserEmail . "\001auth=Bearer " . $token . "\001\001";
		//~ sys::logger( sprintf( 'OAuthGoogle : getOauth64 :: %s', $key));
		//~ sys::logger( sprintf( 'OAuthGoogle : getOauth64 :: %s', base64_encode( $key)));

		return ( base64_encode( $key));

	}

}
