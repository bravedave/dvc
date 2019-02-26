<?php
/**
 * HttpPost allows us to construct and send a POST request to
 * another web server
 *
 * @author Tony Gaitatzis
 */
Namespace dvc;

class HttpPost {
	public $debug = false;
	public $url;
	public $postString;
	public $httpResponse;

	public $ch;

	/**
	 * Constructs an HttpPost object and initializes CURL
	 *
	 * @param url the url to be accessed
	 */
	public function __construct($url) {
		$this->url = $url;
		$this->ch = curl_init( $this->url );
		curl_setopt( $this->ch, CURLOPT_FOLLOWLOCATION, false );
		curl_setopt( $this->ch, CURLOPT_HEADER, false );
		curl_setopt( $this->ch, CURLOPT_RETURNTRANSFER, true );
	}

	/**
	 * shut down CURL before destroying the HttpPost object
	 */
	public function __destruct() {
		curl_close($this->ch);
	}

	/**
	 * Convert an incoming associative array into a POST string
	 * for use with our HTTP POST
	 *
	 * @param params an associative array of data pairs
	 */
	public function setPostData($params) {
		// http_build_query encodes URLs, which breaks POST data
		$this->postString = rawurldecode(http_build_query( $params ));

		if ( $this->debug) \sys::logger( $this->postString);

		curl_setopt( $this->ch, CURLOPT_POST, true );
		curl_setopt ( $this->ch, CURLOPT_POSTFIELDS, $this->postString );
	}

	/**
	 * Make the POST request to the server
	 */
	public function send() {
		$this->httpResponse = curl_exec( $this->ch );
	}
	/**
	 * Read the HTTP Response returned by the server
	 */
	public function getResponse() {
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
