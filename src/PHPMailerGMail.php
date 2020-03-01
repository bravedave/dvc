<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 *  DO NOT change this file
 *  Copy it to <application>/app/dvc/ and modify it there
*/

class PHPMailerGMail extends PHPMailerOAuth {
	public function getOAUTHInstance() {
		if (!is_object($this->oauth))
			$this->oauth = new dvc\OAuthGoogle( currentUser::user()->email );

		return $this->oauth;

	}

}
