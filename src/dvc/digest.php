<?php
/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * This work is licensed under a Creative Commons Attribution 4.0 International Public License.
 * 	http://creativecommons.org/licenses/by/4.0/
 *
 **/

Namespace dvc;

class digest {
  var $digestParts = [];
  var $realm = '';

  function authorized( $A1) {
    return ( $this->authorised( $A1));

  }

  function authorised( $A1) {
    $A2 = md5( $_SERVER['REQUEST_METHOD'] . ':' . $this->digestParts['uri'] );
    $valid_response = md5( sprintf( '%s:%s:%s:%s:%s:%s',
      $A1,
      $this->digestParts['nonce'],
      $this->digestParts['nc'],
      $this->digestParts['cnonce'],
      $this->digestParts['qop'],
      $A2)

    );

    return ( $this->digestParts['response'] == $valid_response);

  }

  protected function getDigest() {
    // return $this->request->getHeader('Authorization');
    return ( $_SERVER['PHP_AUTH_DIGEST']);

  }

  /**
  * Returns digest hash for a @key / @secret.
  *
  * @return string|null
  */
	function getDigestHash($key, $secret) {
			return ( md5( sprintf( '%s:%s:%s', $key, $this->realm, $secret)));

		return NULL;

	}

  /**
  * Returns the username for the request
  *
  * @return string
  */
  function getUsername() {
    return $this->digestParts['username'];

  }

  protected function hasHeader() {
    return ( !(empty($_SERVER['PHP_AUTH_DIGEST'])));

  }

  function requireAuth() {
    header('HTTP/1.1 401 Unauthorized');
    header( sprintf( 'WWW-Authenticate: Digest realm="%s",qop="auth",nonce="%s",opaque="%s"', $this->realm, uniqid(), md5($this->realm)));

    throw new \Exception( "No 'Authorization: Digest' header found.");

  }

  /**
  * Parses the different pieces of the digest string into an array.
  *
  * @param string $digest
  * @return false|array
  */
  protected function parseDigest($digest) {
    // protect against missing data
    $needed_parts = ['nonce' => 1, 'nc' => 1, 'cnonce' => 1, 'qop' => 1, 'username' => 1, 'uri' => 1, 'response' => 1];
    $data = [];

    preg_match_all('@(\w+)=(?:(?:")([^"]+)"|([^\s,$]+))@', $digest, $matches, PREG_SET_ORDER);

    foreach ($matches as $m) {
      $data[$m[1]] = $m[2] ? $m[2] : $m[3];
      unset($needed_parts[$m[1]]);

    }

    return $needed_parts ? false : $data;

  }

  function __construct( $realm = 'DVC') {
    $this->realm = $realm;

    $this->hasHeader() ?
      $this->digestParts = $this->parseDigest( $this->getDigest()) :
      $this->requireAuth();

  }

}
