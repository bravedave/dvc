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
		$this->params = [];

		curl_setopt( $this->ch, CURLOPT_FOLLOWLOCATION, false );
		curl_setopt( $this->ch, CURLOPT_HEADER, false );
		curl_setopt( $this->ch, CURLOPT_RETURNTRANSFER, true );

	}

	public function __destruct() {
		curl_close($this->ch);	/* shut down CURL before destroying the HttpGet object */

	}

	public function getResponseJSON() {
		return json_decode( $this->getResponse());

	}

	public function getResponse() {	/* Read the HTTP Response returned by the server */
		return $this->httpResponse;

	}

	public function getResponseDecoded() {
		$aR = [];
		$aResponse = explode( '&', $this->getResponse());
		foreach ( $aResponse as $res ) {
			$aRes = explode( '=', $res );
			if ( isset( $aRes[0] ) && isset( $aRes[1])) {
				$aR[ $aRes[0]] = urldecode( $aRes[1]);

			}

		}
		return ( $aR);

	}

	public function send() {	/* Make the GET request to the server */
		curl_setopt($this->ch, CURLOPT_URL, $this->url_builder());
		$this->httpResponse = curl_exec( $this->ch );
		return ( $this->httpResponse);

	}

	public function setHTTPHeaders($headers) {
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);

	}

	public function setProxy( $proxy) {
		/*
			$proxy can be : socks5://localhost:1080

			// ssh -f -N -D 1080 -p <port> <user@server>

			dnf install autossh
			autossh -f -N -D 1080 -p <port> -M 0 -o "ServerAliveInterval 60" -o "ServerAliveCountMax 3" <user@server>

			Reference: https://www.everythingcli.org/ssh-tunnelling-for-fun-and-profit-autossh/

			On Startup:
			mc -e /etc/systemd/system/autossh-dynamic-tunnel.service

			[Unit]
			Description=AutoSSH Dynamic Proxy Tunnel on 1080
			After=network.target

			[Service]
			Environment="AUTOSSH_GATETIME=0"
			ExecStart=/usr/bin/autossh -N -D 1080 -p <port> -M 0 -o "ServerAliveInterval 60" -o "ServerAliveCountMax 3" <user@server>

			[Install]
			WantedBy=multi-user.target

			Then
			systemctl daemon-reload
			systemctl start autossh-dynamic-tunnel.service

			at bootime
			systemctl enable autossh-dynamic-tunnel.service

		*/

		curl_setopt( $this->ch, CURLOPT_PROXY, $proxy);

	}

	public function setReferer($referer) {	/* Set the HTTP Referer header */
		curl_setopt($this->ch, CURLOPT_REFERER, $referer);

	}

	public function setUserAgent( $agent) {
		curl_setopt($this->ch, CURLOPT_USERAGENT, $agent);

	}

	public function url_builder() {
		if ( count($this->params) > 0 )
			return ( $this->url . '?' . http_build_query( $this->params));

		return ( $this->url);

	}

}
