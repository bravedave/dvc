<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\dvc\controller;

use config, Controller, currentUser, dvc, HttpGet, HttpPost, Response, sys;

class fbauth extends Controller {
  protected $RequireValidation = FALSE;

  static $oauth2_token_url = 'https://graph.facebook.com/oauth/access_token';
  static $oauth2_access_url = 'https://graph.facebook.com/me';
  static $access_dialog_url = 'https://www.facebook.com/dialog/oauth';

  protected function before() {
    self::application()::app()->exclude_from_sitemap = true;
    parent::before();
  }

  public function request() {

    if (dvc\auth::FacebookAuthEnabled()) {
      /*
				https://www.facebook.com/v2.5/dialog/oauth?
					response_type=token
					display=popup
					client_id=<< clientid >>
					redirect_uri=<< callback url >>
					scope=email

			*/
      $params = array(
        'client_id' => config::$facebook_oauth2_client_id,
        'response_type' => 'code',
        'redirect_uri' => config::$facebook_oauth2_redirect,
        'scope' => 'email'
      );

      Response::redirect(self::$access_dialog_url . '?' .  http_build_query($params));
    } else
      sys::logger('facebook authentication is disabled');
  }

  public function response() {

    if (dvc\auth::FacebookAuthEnabled()) {
      /**
       * the OAuth server should have brought us to this page with a $_GET['code']
       */

      $code = $this->getParam('code');
      if ($code == '') {
        sys::logger('fbAuth: no code retrieved');
      } else {

        /* try to get an access token and build
				 * our POST data to send back to the
				 * OAuth server in exchange for and access_token
				 */
        $params = array(
          'code' => $code,
          'client_id' => config::$facebook_oauth2_client_id,
          'client_secret' => config::$facebook_oauth2_secret,
          'redirect_uri' => config::$facebook_oauth2_redirect
        );

        // build a new HTTP POST request
        $request = new HttpPost(self::$oauth2_token_url);
        $request->setPostData($params);
        $request->send();

        /*--- ---[decode the incoming string as URL_ENCODED]--- ---
					The Request now looks like this
					httpResponse will need to be split

					HttpPost Object
					(
						[url] => https://graph.facebook.com/oauth/access_token
						[postString] => << our request >>
						[httpResponse] => access_token=<< the token >>&expires=5168698
						[ch] => Resource id #
					)

					get an object with at lease access_token defined (may be empty) */
        $responseObj = (object)array_merge(array('access_token' => ''), $request->getResponseDecoded());
        //~ sys::dump( $responseObj);
        /*--- ---[decode the incoming string as URL_ENCODED]--- ---*/

        if ($responseObj->access_token != '') {
          /* Tada: we have an access token! */

          $token = $responseObj->access_token;
          dvc\session::edit();

          $_SESSION['access_token'] = $token;

          $response = new HttpGet(self::$oauth2_access_url);
          $response->params['fields'] = 'id,name,first_name,last_name,email,picture';
          $response->params['access_token'] = $token;
          $response->send();

          $responseObj = json_decode($response->getResponse());
          //~ echo "User Info: <pre>";
          //~ print $response;
          //~ sys::dump( $response );
          //~ sys::dump( $responseObj );

          if ((string)$responseObj->email != '') {
            /*
						 * So here we are saying we accept any user that bothers
						 * to identify with a valid facebook account
						 */
            $oauth = new dvc\oauth();
            $oauth->displayName = $responseObj->name;
            $oauth->Surname =  $responseObj->last_name;
            $oauth->GivenNames =  $responseObj->first_name;
            $oauth->email = $responseObj->email;
            currentUser::sync($oauth);

            $_SESSION["DisplayName"] = $oauth->displayName;
            $_SESSION["Email"] = $oauth->email;
            if (isset($responseObj->picture))
              $_SESSION["avatar"] = $responseObj->picture->data->url;
          } else {

            sys::logger('fbAuth: user not valid : ' . (string)$responseObj->email);
          }
        } else {

          sys::logger('fbAuth: no access token retrieved');
        }
      }
    } else {

      sys::logger('facebook authentication is disabled');
    }

    Response::redirect();
  }
}
