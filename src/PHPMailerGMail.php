<?php
/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * This work is licensed under a Creative Commons Attribution 4.0 International Public License.
 * 	http://creativecommons.org/licenses/by/4.0/
 *
 * DO NOT change this file
 * Copy it to <application>/app and modify it there
 */

class PHPMailerGMail extends PHPMailerOAuth {
	public function getOAUTHInstance() {
		if (!is_object($this->oauth))
			$this->oauth = new dvc\OAuthGoogle( currentUser::user()->email );

		return $this->oauth;

	}

}
