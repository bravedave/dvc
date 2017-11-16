<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/
NameSpace dvc\ews;

class credentials {
	var $account, $password, $server;

	function __construct( $_user, $_pass, $_server = NULL) {
		$this->account = $_user;
		$this->password = $_pass;

		if ( is_null( $_server)) {
			if ( isset( \config::$exchange_server))
				$this->server = \config::$exchange_server;
			else
				throw new Exceptions\InvalidExchangeServer;

		}
		else {
			$this->server = $_server;

		}

	}

	static function getCurrentUser() {
		return \currentUser::exchangeAuth();

	}

}
