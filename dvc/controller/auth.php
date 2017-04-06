<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/

class auth extends Controller {
	public $RequireValidation = FALSE;
	//~ public $debug = TRUE;

	public function request() {
		if ( $this->debug) sys::logger( 'auth/request');

		if ( dvc\auth::GoogleAuthEnabled()) {
			$client = new dvc\GoogleAPI\client;
			$url = $client->getAuthUrl();
			//~ print $url;
			Response::redirect( $url);

		}
		else {
			throw new dvc\GoogleAPI\Exceptions\not_enabled;

		}

	}

	public function response() {
		if ( dvc\auth::GoogleAuthEnabled()) {
			/**
			 * the OAuth server should have brought us to this page with a $_GET['code']
			 * an error will be thrown in getAccessToken if the code is not valid
			 */
			$client = new dvc\GoogleAPI\client;
				//~ $client->debug = $this->debug;
			if ( $token = $client->getAccessToken( $this->getParam( 'code'))) {
				\dvc\session::set('access_token', $token);
				\dvc\session::set('refresh_token', $client->getRefreshToken());
				\dvc\session::set('credentials', $client->getCredentials());

				$me = $client->me();
				//~ sys::dump( $me);

				if ( isset( $me->emails)) {
					if ( (string)$me->emails[0]->value != '' ) {
						/*
						 * So here we are saying we accept any user that
						 * bothers to identify with a valid google account
						 * ----------------
						 * noting that any user created would be created
						 * in \currentUser::sync. It's not the role of
						 * this routing to say what the authenticated user
						 * can or cannot access
						 */
						$oauth = new dvc\oauth();
							$oauth->displayName = $me->displayName;
							$oauth->Surname =  $me->name->familyName;
							$oauth->GivenNames =  $me->name->givenName;
							$oauth->email = $me->emails[0]->value;
						\currentUser::sync( $oauth);

						$_SESSION["DisplayName"] = $oauth->displayName;
						$_SESSION["Email"] = $oauth->email;
						$_SESSION["avatar"] = $me->image->url;

					}
					else {
						throw new \Exceptions\InvalidAuthUser;

					}

				}
				else {
					throw new \Exceptions\InvalidAuthUser;

				}

			}

		}
		else {
			throw new dvc\GoogleAPI\Exceptions\not_enabled;

		}

		\Response::redirect();

	}

}
