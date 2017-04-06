<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International License.
		http://creativecommons.org/licenses/by/4.0/

	*/
Namespace dvc\GoogleAPI;

class client {
	var $debug = FALSE;

	protected $key = '';
	protected $credentials = '';
	protected $accessToken = '';
	protected $expires = 0;	// time
	protected $_refreshToken = '';
	//~ protected $scopes = array(
		//~ 'https://www.googleapis.com/auth/plus.me',
		//~ 'https://www.googleapis.com/auth/userinfo.email' );
	protected $scopes = array(
		'profile',
		'email',
		'openid' );

	const oauth2_token_url = 'https://accounts.google.com/o/oauth2/token';
	const oauth2_server_url = 'https://accounts.google.com/o/oauth2/auth';
	const oauth2_access_url = 'https://www.googleapis.com/plus/v1/people/me';

	function __construct() {
		$this->key = \config::$google_api_key;

		if ( \config::$oauth2_scope == \config::GMAIL_ALL)
			$this->scopes[] = 'https://mail.google.com/';
		elseif ( \config::$oauth2_scope == \config::GMAIL_SEND)
			$this->scopes[] = 'https://www.googleapis.com/auth/gmail.send';
		elseif ( \config::$oauth2_scope == \config::GMAIL_READ)
			$this->scopes[] = 'https://www.googleapis.com/auth/gmail.readonly';
		elseif ( \config::$oauth2_scope == \config::GMAIL_COMPOSE)
			$this->scopes[] = 'https://www.googleapis.com/auth/gmail.compose';
		elseif ( \config::$oauth2_scope == \config::GMAIL_COMPOSE_SEND_READ) {
			$this->scopes[] = 'https://www.googleapis.com/auth/gmail.compose';
			$this->scopes[] = 'https://www.googleapis.com/auth/gmail.readonly';
			$this->scopes[] = 'https://www.googleapis.com/auth/gmail.send';

		}

	}

	protected function getAuthParams() {
		$params = array(
			'scope' => implode( ' ', $this->scopes),
			'access_type' => 'offline',
			'include_granted_scopes' => 'true',
			'redirect_uri' => \config::$oauth2_redirect,
			'response_type' => 'code',
			'client_id' => \config::$oauth2_client_id);

		return ( $params);

	}

	public function getAuthUrl() {
		$params = $this->getAuthParams();
		return ( self::oauth2_server_url . '?' . http_build_query($params));

	}

	public function setAccessToken( $token) {
		$this->accessToken = $token;

	}

	public function getAccessToken( $code = '') {
		if ( (string)$code == '')
			throw new Exceptions\invalid_access_code;

		/***
		 * try to get an access token and build our POST data to send
		 * back to the OAuth server in exchange for and access_token
		 */
		$params = array(
			'code' => $code,
			'client_id' => \config::$oauth2_client_id,
			'client_secret' => \config::$oauth2_secret,
			'redirect_uri' => \config::$oauth2_redirect,
			'grant_type' => 'authorization_code' );

		// build a new HTTP POST request
		$request = new \HttpPost( self::oauth2_token_url);
			$request->setPostData($params);
			$request->send();

		$this->credentials =  $request->getResponse();
		// decode the incoming string as JSON
		$response = json_decode( $this->credentials);
		//~ \sys::dump( $response);
		/*
			stdClass Object (
				[access_token] => ya29.GlsaBI39BTo blah blah FQUhx4bb2r06lyDseH9IRQReYEOp13fKlRD8nGn_StFbpFamV
				[expires_in] => 3599
				[id_token] => eyJhbGciOiJSUzI1NiIsIm blah blah rNs-kPM0uQZezaIqVQ
				[token_type] => Bearer
			)	*/

		if ( isset( $response->access_token) && $response->access_token != '' ) {
			$this->accessToken = $response->access_token;
			if ( $this->debug) \sys::logger( sprintf( 'dvc\GoogleAPI->client :: accessToken  : %s', $this->accessToken));

			$response->expires_at = time() + (int)$response->expires_in;
			$this->credentials =  json_encode( $response);
			$this->request_offline_token( $this->accessToken);
			return ( $this->accessToken);

		}
		elseif ( isset( $response->error)) {
			if ( isset( $response->error_description))
				throw new \Exception( 'g-oauth :: ' . $response->error_description);

			else
				throw new \Exception( 'g-oauth :: ' . $response->error);

		}
		else {
			throw new Exceptions\no_access_token_received;
			//~ if ( $debug) \sys::dump( $responseObj);

		}

		return ( FALSE);

	}

	public function RefreshToken( $token) {
		if ( (string)$token == '')
			throw new Exceptions\invalid_refresh_token;

		//~ $params = $this->getAuthParams();
		//~ $params += ['approval_prompt' => 'auto'];

		/***
		 * try to get an access token and build our POST data to send
		 * back to the OAuth server in exchange for and access_token
		 */
		$params = array(
			'client_id' => \config::$oauth2_client_id,
			'client_secret' => \config::$oauth2_secret,
			'refresh_token' => $token,
			'grant_type' => 'refresh_token' );

		// build a new HTTP POST request
		$request = new \HttpPost( self::oauth2_token_url);
			$request->debug = $this->debug;

			$request->setPostData($params);
			$request->send();

		$this->credentials =  $request->getResponse();
		// decode the incoming string as JSON
		$response = json_decode( $this->credentials);
		//~ \sys::dump( $response);
		/*
			stdClass Object (
				[access_token] => ya29.GlsaBI39BTo blah blah FQUhx4bb2r06lyDseH9IRQReYEOp13fKlRD8nGn_StFbpFamV
				[expires_in] => 3599
				[id_token] => eyJhbGciOiJSUzI1NiIsIm blah blah rNs-kPM0uQZezaIqVQ
				[token_type] => Bearer
			)	*/

		if ( isset( $response->access_token) && $response->access_token != '' ) {
			$this->accessToken = $response->access_token;
			$response->expires_at = time() + (int)$response->expires_in;
			$this->credentials =  json_encode( $response);
			return ( $this->accessToken);

		}
		elseif ( isset( $response->error)) {
			if ( isset( $response->error_description))
				throw new \Exception( 'g-oauth :: ' . $response->error_description);

			else
				throw new \Exception( 'g-oauth :: ' . $response->error);

		}
		else {
			throw new Exceptions\no_access_token_received;
			//~ if ( $debug) \sys::dump( $responseObj);

		}

		return ( FALSE);

	}

	public function getCredentials() {
		return ( $this->credentials);

	}

	public function setCredentials( $s) {
		$this->credentials = (string)$s;

	}

	public function getRefreshToken() {
		return ( $this->_refreshToken);

	}

	public function isAccessTokenExpired() {
		$creds = json_decode( $this->credentials);
		/*
			stdClass Object (
				[access_token] => ya29.GlsaBI39BTo blah blah FQUhx4bb2r06lyDseH9IRQReYEOp13fKlRD8nGn_StFbpFamV
				[expires_in] => 3599
				[id_token] => eyJhbGciOiJSUzI1NiIsIm blah blah rNs-kPM0uQZezaIqVQ
				[token_type] => Bearer
			)	*/

		if ( isset( $creds->expires_at))
			return ( (int)$creds->expires_at < time());

		return ( TRUE);

	}

	public function lifeTime() {
		$creds = json_decode( $this->credentials);
		/*
			stdClass Object (
				[access_token] => ya29.GlsaBI39BTo blah blah FQUhx4bb2r06lyDseH9IRQReYEOp13fKlRD8nGn_StFbpFamV
				[expires_in] => 3599
				[id_token] => eyJhbGciOiJSUzI1NiIsIm blah blah rNs-kPM0uQZezaIqVQ
				[token_type] => Bearer
			)	*/

		if ( isset( $creds->expires_at))
			return ( (int)$creds->expires_at - time());

		return ( 0);

	}

	public function request_offline_token( $token) {
		/*
		 * this makes a request to the Google API, using Curl to get another access token that we can use
		 * for authentication on the Gmail API when sending messages
		 */
		$params = array(
			'grant_type' => 'refresh_token',
			'client_id' => \config::$oauth2_client_id,
			'client_secret' => \config::$oauth2_secret,
			'refresh_token' => $token);
		if ( $this->debug) \sys::logger( print_r( $params, TRUE));

		// build a new HTTP POST request
		$request = new \HttpPost( self::oauth2_token_url);
			$request->debug = $this->debug;
			$request->setPostData( $params);
			$request->send();


		// decode the incoming string as JSON
		$response = json_decode( $request->getResponse());
		//~ \sys::dump( $response);
		/*
			stdClass Object (
				[access_token] => ya29.GlsaBI39BTo blah blah FQUhx4bb2r06lyDseH9IRQReYEOp13fKlRD8nGn_StFbpFamV
				[expires_in] => 3599
				[id_token] => eyJhbGciOiJSUzI1NiIsIm blah blah rNs-kPM0uQZezaIqVQ
				[token_type] => Bearer
			)	*/


		if ( isset( $response->access_token) && $response->access_token != '' ) {
			$this->_refreshToken = $response->access_token;
			if ( $this->debug) \sys::logger( sprintf( 'dvc\GoogleAPI->client :: refreshToken : %s', $this->_refreshToken));
			return ( $this->_refreshToken);

		}
		elseif ( isset( $response->error)) {
			if ( isset( $response->error_description)) {
				if ( $this->debug) \sys::logger( sprintf( 'dvc\GoogleAPI->client :: refreshToken : %s', $response->error_description));
				//~ throw new \Exception( 'g-oauth :: ' . $response->error_description);

			}
			else {
				if ( $this->debug) \sys::logger( sprintf( 'dvc\GoogleAPI->client :: refreshToken : %s', $response->error));
				//~ throw new \Exception( 'g-oauth :: ' . $response->error);

			}

		}
		else {
			if ( $this->debug) \sys::logger( 'dvc\GoogleAPI->client :: refreshToken : no_access_token_received');
			//~ throw new Exceptions\no_access_token_received;

		}

		return FALSE;

	}

	public function me() {
		$response = new \HttpGet( self::oauth2_access_url );	// 'https://www.googleapis.com/plus/v1/people/me');
			$response->params['access_token'] = $this->accessToken;
			$response->send();

		$response = json_decode( $response->getResponse());

		return ( $response);

	}

}
