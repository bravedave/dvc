<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc;

abstract class Google {
  static $debug = FALSE;
  //~ static $debug = TRUE;

  static function client() {
    if (class_exists('Google_Client')) {

      $client = new \Google_Client();
      $client->setClientId(\config::$oauth2_client_id);
      $client->setClientSecret(\config::$oauth2_secret);

      $client->setAccessType('offline');        // offline access
      $client->setIncludeGrantedScopes(true);   // incremental auth

      $client->addScope('profile');
      $client->addScope('email');
      $client->addScope('openid');

      if (\config::$oauth2_scope == \config::GMAIL_ALL)
        $client->addScope('https://mail.google.com/');
      elseif (\config::$oauth2_scope == \config::GMAIL_SEND)
        $client->addScope('https://www.googleapis.com/auth/gmail.send');
      elseif (\config::$oauth2_scope == \config::GMAIL_READ)
        $client->addScope('https://www.googleapis.com/auth/gmail.readonly');
      elseif (\config::$oauth2_scope == \config::GMAIL_COMPOSE)
        $client->addScope('https://www.googleapis.com/auth/gmail.compose');
      elseif (\config::$oauth2_scope == \config::GMAIL_COMPOSE_SEND_READ) {
        $client->addScope('https://www.googleapis.com/auth/gmail.compose');
        $client->addScope('https://www.googleapis.com/auth/gmail.readonly');
        $client->addScope('https://www.googleapis.com/auth/gmail.send');
      }

      $client->setRedirectUri(\config::$oauth2_redirect);

      return $client;
    }

    return null;
  }

  static function saveSession($client) {
    // You can retrieve the access token with the getAccessToken method:
    $token = $client->getAccessToken();
    //~ print $token;

    //~ sys::dump( $token);
    $j = (object)$token;
    /*
		stdClass Object (
			[access_token] => ya29.GlsaBI39BTo blah blah FQUhx4bb2r06lyDseH9IRQReYEOp13fKlRD8nGn_StFbpFamV
			[expires_in] => 3599
			[id_token] => eyJhbGciOiJSUzI1NiIsIm blah blah rNs-kPM0uQZezaIqVQ
			[token_type] => Bearer
		)	*/
    if (isset($j->access_token) && $j->access_token != '') {
      if (self::$debug) {
        if (\session::get('access_token') && \session::get('access_token') != $j->access_token) {
          \sys::logger('google access token changed');
        }
      }

      \dvc\session::set('access_token', $j->access_token);
    }

    //~ \dvc\session::set('refresh_token', $client->getRefreshToken());
    \dvc\session::set('credentials', json_encode($token));
  }

  static function validate($client) {
    if (\session::get('access_token')) {
      $client->setAccessToken(session::get('access_token'));
      self::saveSession($client);  //\session::set( 'access_token', $client->getAccessToken());
      return (TRUE);
    }

    return (FALSE);
  }

  static function redirect($client) {
    \Response::redirect($client->createAuthUrl());
  }

  static function plus($client = NULL) {
    if (is_null($client)) {
      $client = self::client();
    }

    $plus = new \Google_Service_Plus($client);
    return ($plus);
  }

  static function mailer() {
    // Create a new PHPMailer instance
    $mail = new \PHPMailerGMail;   // this must be the custom class we created
    $mail->isSMTP();        // Tell PHPMailer to use SMTP

    //~ $mail->SMTPDebug = 2;		// Enable SMTP debugging
    //~ $mail->Debugoutput = 'html';	// Ask for HTML-friendly debug output

    $mail->AuthType = 'XOAUTH2';  // Set AuthType
    $mail->SMTPAuth = true;    // Whether to use SMTP authentication
    $mail->SMTPSecure = 'tls';    // Set the encryption system to use - ssl (deprecated) or tls
    $mail->Host = 'smtp.gmail.com';  // Set the hostname of the mail server

    $mail->Port = 587;        // Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission

    // User Email to use for SMTP authentication - Use the same Email used in Google Developer Console
    $mail->oauthUserEmail = \currentUser::user()->email;

    $mail->oauthClientId = \config::$oauth2_client_id;  //Obtained From Google Developer Console
    $mail->oauthClientSecret = \config::$oauth2_secret;  //Obtained From Google Developer Console

    $mail->oauthRefreshToken = \session::get('access_token');

    return $mail;
  }
}
