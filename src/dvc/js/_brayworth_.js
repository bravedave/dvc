/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */
/*jshint esversion: 6 */
if ( !window._brayworth_ )
	window._brayworth_ = () => { return ( window._brayworth_); };

(_ => {
	_.version = 0.2;
	_._brayworth_ = true;
	_.currentUser = false;
	_.logon_retrieve_password = false;
	_.templates = {};

	_.hideContexts = () => {
		$(document).trigger('hide-contexts');

	};

	_.bootstrap_version = () => {
		if ( 'undefined' != typeof bootstrap) {
			if ( !!bootstrap.Alert) {
				if ( /4/.test( bootstrap.Alert.VERSION)) {
					return 4;

				}
				else if ( /3/.test( bootstrap.Alert.VERSION)) {
					return 3;

				}

			}

		}

		return 0;

  };

  _.REMtoPX = i => {
    let rem = parseFloat(getComputedStyle($(':root')[0]).fontSize);
    return (rem * i);

  };

  _.desktop = {
    width: 64,
    height: 32,
    isSmall: () => {
      if ($(window).width() < _.REMtoPX(_.desktop.width)) return true;
      if ($(window).height() < _.REMtoPX(_.desktop.height)) return true;

      return false;

    }

  };

  _.timezone = '';

  $(document).ready( () => {
    dayjs.extend(dayjs_plugin_localizedFormat);
    dayjs.extend(dayjs_plugin_utc);
    dayjs.extend(dayjs_plugin_timezone);

    if ('' !== _.timezone) {
      dayjs.tz.setDefault(_.timezone);

    }

  });

  _.dayjs = ( a,b,c,d) => {
    let r = dayjs( a,b,c,d);

    if ('' !== _.timezone) r.tz(_.timezone);

		return (r);

	};

  _.getMeta = (mName) => {
    let metas = document.getElementsByTagName('meta');

    for (let i = 0; i < metas.length; i++) {
      if (metas[i].getAttribute('name') === mName) {
        return metas[i].getAttribute('content');

      }

    }

    return '';

  }

	_.moment = ( a,b,c,d) => {
		/**
		* if you call this and the moment library
		* is undefined it will error (der)
		*
		* The intention is that:
		*	a. the library will be loaded
    *	b. you could/will redefine this function to control
    * 		 the timezone being operated in
    */

		let r = moment( a,b,c,d);
		// d.utcOffset( desirable timezone);
		return (r);

	};

	_.nav = (_url, withProtocol) => {
		if ( _.browser.isIPhone) {
			$(window).on('pagehide', () => _.hourglass.off());

		}
		else {
			$(window).on('unload', () => _.hourglass.off());

		};

		_.hourglass.on();
		window.location.href = _.url(_url, withProtocol);

	};

  _.urlwrite = _.url = ( _url, withProtocol) => {
		if ( 'undefined' == typeof _url)
			_url = '';

		let prefix = !!withProtocol ? location.protocol + '//' : '/'

		return ( prefix + _url);

	};

})(_brayworth_);
