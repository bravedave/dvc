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

((_) => {
	_.version = 0.1;
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

	_.urlwrite = _.url = ( _url) => {
		if ( 'undefined' == typeof _url)
			_url = '';

		return ( '/' + _url);

	};

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

})(_brayworth_);
