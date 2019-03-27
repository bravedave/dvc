_brayworth_.urlwrite = _brayworth_.url = function( _url, withProtocol) {
	if ( 'undefined' == typeof _url)
		_url = '';

	if ( !!withProtocol) {
		return ( '<?= sprintf( '%s%s', url::$PROTOCOL, url::$URL) ?>' + _url);

	}
	else {
		return ( '<?= url::$URL ?>' + _url);

	}

};
