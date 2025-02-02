<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\dvc;

class http {
	public $ch;
	public $debug = false;
	public $httpResponse;
	public $params;
	public $postString;
	public $url;

	protected array $_headers = [];

	/**
	 * Constructs an http suitable for a get request using curl CURL
	 *
	 * @param url the url to be accessed
	 * @param _options the overrides the default setup for curl
	 */
	public function __construct($_url, $_options = []) {

		$this->url = $_url;
		$this->ch = curl_init();
		$this->params = [];

		$options = array_merge([
			'CURLOPT_FOLLOWLOCATION' => 0,
			'CURLOPT_HEADER' => 0,
			'CURLOPT_RETURNTRANSFER' => 1
		], $_options);

		array_walk($options, fn($o, $k) => curl_setopt($this->ch, constant($k), $o));
	}

	public function __destruct() {

		curl_close($this->ch);	/* shut down CURL before destroying the object */
	}

	/** @return string  */
	public function error(): string {

		return curl_error($this->ch);
	}

	public function getResponseJSON() {

		return json_decode($this->getResponse());
	}

	public function getResponse() {	/* Read the HTTP Response returned by the server */

		return $this->httpResponse;
	}

	public function getResponseDecoded() {

		$aR = [];
		$aResponse = explode('&', $this->getResponse());
		foreach ($aResponse as $res) {

			$aRes = explode('=', $res);
			if (isset($aRes[0]) && isset($aRes[1])) {

				$aR[$aRes[0]] = urldecode($aRes[1]);
			}
		}

		return $aR;
	}

	public function post(object|array $params, $useJson = true): string|bool {

		$this->setPostData($params, $useJson);
		return $this->send();
	}

	public function send(): string|bool {	/* Make the GET request to the server */

		curl_setopt($this->ch, CURLOPT_URL, $this->url_builder());
		$this->httpResponse = curl_exec($this->ch);

		if (curl_errno($this->ch)) {

			$response = 'cURL error: ' . curl_error($this->ch);
			logger::info(sprintf('<%s> %s', $response, logger::caller()));
		}

		return $this->httpResponse;
	}

	public function setHTTPHeaders(array $headers): array {

		foreach ($headers as $header) {

			/*
			 if the header has a colon, it will be a key/value pair
			 check the existing headers and if you find one with the same key
			 overwrite it with the new value
			 */

			if (strpos($header, ':') !== false) {

				list($key, $value) = explode(':', $header, 2);
				$key = trim($key);
				$value = trim($value);
				$this->_headers[$key] = $header;
			} else {

				// else just add the header
				$this->_headers[] = $header;
			}
		}

		$headers = array_map(fn($h) => (string)$h, $this->_headers);
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);

		return $headers;
	}

	public function setPostData(object|array $data, $useJson = true): void {

		/**
		 * 2024--09-10
		 * http_build_query loses "+" characters
		 * so encode the array into a JSON string
		 * and set the content type to application/json
		 */

		curl_setopt($this->ch, CURLOPT_POST, true);

		if ($useJson) {

			$this->postString = json_encode($data);
			$this->setHTTPHeaders(['Content-Type: application/json']);

			if ($this->debug) logger::debug($this->postString);

			curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->postString);
		} else {

			// they are probably sending a CurlFile object
			curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
		}
	}

	public function setPostData_legacy(object|array $params): void {
		// http_build_query encodes URLs, which breaks POST data
		$this->postString = rawurldecode(http_build_query($params));

		if ($this->debug) logger::debug($this->postString);

		curl_setopt($this->ch, CURLOPT_POST, true);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->postString);
	}

	public function setProxy($proxy) {

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

		curl_setopt($this->ch, CURLOPT_PROXY, $proxy);
	}

	public function setReferer($referer) {	/* Set the HTTP Referer header */

		curl_setopt($this->ch, CURLOPT_REFERER, $referer);
	}

	public function setUserAgent($agent) {

		curl_setopt($this->ch, CURLOPT_USERAGENT, $agent);
	}

	public function url_builder() {

		if (count($this->params) > 0) return ($this->url . '?' . http_build_query($this->params));
		return $this->url;
	}
}
