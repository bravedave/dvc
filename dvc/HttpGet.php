<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/
Namespace dvc;

class HttpGet {
	public $url;
	public $ch;
	public $params;
	public $httpResponse;

	/**
	 * Constructs an HttpGet object and initializes CURL
	 *
	 * @param url the url to be accessed
	 */
	public function __construct($url) {
		$this->url = $url;
		$this->ch = curl_init();
		$this->params = array();

		curl_setopt( $this->ch, CURLOPT_FOLLOWLOCATION, false );
		curl_setopt( $this->ch, CURLOPT_HEADER, false );
		curl_setopt( $this->ch, CURLOPT_RETURNTRANSFER, true );

	}

	/**
	 * shut down CURL before destroying the HttpGet object
	 */
	public function __destruct() {
		curl_close($this->ch);

	}
	/**
	 * Set the HTTP Referer header
	 */
	public function setReferer($referer) {
		curl_setopt($this->ch, CURLOPT_REFERER, $referer);

	}

	public function setHTTPHeaders($headers) {
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);

	}

	public function setUserAgent( $agent) {
		curl_setopt($this->ch, CURLOPT_USERAGENT, $agent);

	}

	public function url_builder() {
		if ( count($this->params) > 0 )
			return ( $this->url . '?' . http_build_query( $this->params));

		return ( $this->url);

	}

	/**
	 * Make the GET request to the server
	 */
	public function send() {
		curl_setopt($this->ch, CURLOPT_URL, $this->url_builder());
		$this->httpResponse = curl_exec( $this->ch );
		return ( $this->httpResponse);

	}
	/**
	 * Read the HTTP Response returned by the server
	 */
	public function getResponse() {
		return $this->httpResponse;

	}

	public function getResponseDecoded() {
		$aR = array();
		$aResponse = explode( '&', $this->getResponse());
		foreach ( $aResponse as $res ) {
			$aRes = explode( '=', $res );
			if ( isset( $aRes[0] ) && isset( $aRes[1])) {
				$aR[ $aRes[0]] = urldecode( $aRes[1]);

			}

		}
		return ( $aR);

	}

}
