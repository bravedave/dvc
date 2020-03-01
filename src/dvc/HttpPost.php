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

class HttpPost {
	public $debug = false;
	public $url;
	public $postString;
	public $httpResponse;

	public $ch;

	public function __construct($url) {
		$this->url = $url;
		$this->ch = curl_init( $this->url );
		curl_setopt( $this->ch, CURLOPT_FOLLOWLOCATION, false );
		curl_setopt( $this->ch, CURLOPT_HEADER, false );
		curl_setopt( $this->ch, CURLOPT_RETURNTRANSFER, true );
	}

	public function __destruct() {
		curl_close($this->ch);
	}

	public function setPostData($params) {
		// http_build_query encodes URLs, which breaks POST data
		$this->postString = rawurldecode(http_build_query( $params ));

		if ( $this->debug) \sys::logger( $this->postString);

		curl_setopt( $this->ch, CURLOPT_POST, true );
		curl_setopt ( $this->ch, CURLOPT_POSTFIELDS, $this->postString );

	}

	public function send() {	/** Make the POST request to the server */
		$this->httpResponse = curl_exec( $this->ch );

	}

	public function getResponseJSON() {
		return json_decode( $this->getResponse());

	}

	public function getResponse() { /** Read the HTTP Response returned by the server */
		return $this->httpResponse;

	}

	public function getResponseDecoded() {
		$aResponse = explode( '&', $this->getResponse());
		$aR = array(
			'access_token' => '' );
		foreach ( $aResponse as $res ) {
			$aRes = explode( '=', $res );
			if ( isset( $aRes[0] ) && isset( $aRes[1])) {
				$aR[ $aRes[0]] = urldecode( $aRes[1]);

			}

		}
		return ( $aR);

	}

}
