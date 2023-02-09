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

use currentUser, Controller, bravedave, sys, dvc, user;
use bravedave\dvc\{session, Response};

class auth extends Controller {

  protected $RequireValidation = false;

  protected function before() {

    self::application()::app()->exclude_from_sitemap = true;
    parent::before();
  }

  public function index() {

    if ($this->getParam('code')) {

      $this->response();
    } else {

      $this->request();
    }
  }

  public function request() {

    if ($this->debug) sys::logger('auth/request');

    if (bravedave\dvc\auth::GoogleAuthEnabled()) {

      if ($client = dvc\Google::client()) {
        $url = $client->createAuthUrl();

        Response::redirect($url);
      } else {

        throw new bravedave\dvc\Exceptions\GoogleAuthNotEnabled;
      }
    } else {

      throw new bravedave\dvc\Exceptions\GoogleAuthNotEnabled;
    }
  }

  public function response() {

    if (bravedave\dvc\auth::GoogleAuthEnabled()) {

      if ($client = \dvc\Google::client()) {

        $client->authenticate($this->getParam('code'));

        dvc\Google::saveSession($client);

        $plus = dvc\Google::plus($client);
        $me = $plus->people->get("me");

        if (isset($me->emails)) {

          if ((string)$me->emails[0]->value != '') {
            /*
             * So here we are saying we accept any user that
             * bothers to identify with a valid google account
             * ----------------
             * noting that any user created would be created
             * in \currentUser::sync. It's not the role of
             * this routing to say what the authenticated user
             * can or cannot access
             */
            $oauth = new bravedave\dvc\oauth();
            $oauth->displayName = $me->displayName;
            $oauth->Surname =  $me->name->familyName;
            $oauth->GivenNames =  $me->name->givenName;
            $oauth->email = $me->emails[0]->value;
            currentUser::sync($oauth);
            session::set('DisplayName', $oauth->displayName);
            session::set('Email', $oauth->email);
            session::set('avatar', $me->image->url);

            user::setGoogleFlag();
          } else {

            throw new bravedave\dvc\Exceptions\InvalidAuthUser;
          }
        } else {

          throw new bravedave\dvc\Exceptions\InvalidAuthUser;
        }
      } else {

        throw new bravedave\dvc\Exceptions\GoogleAuthNotEnabled;
      }
    } else {

      throw new bravedave\dvc\Exceptions\GoogleAuthNotEnabled;
    }

    \Response::redirect();
  }
}
