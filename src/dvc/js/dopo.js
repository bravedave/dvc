( _ => {
  _.timezone = '<?= \config::$TIMEZONE ?>';

  _.urlwrite = _.url = ( _url, withProtocol) => {
    if ( 'undefined' == typeof _url)
      _url = '';

    if ( !!withProtocol) {
      return ( '<?= sprintf( '%s%s', \url::$PROTOCOL, \url::$URL) ?>' + _url);

    }
    else {
      return ( '<?= \url::$URL ?>' + _url);

    }

  };

}) (_brayworth_);
