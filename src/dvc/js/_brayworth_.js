/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * This work is licensed under a Creative Commons Attribution 4.0 International Public License.
 *      http://creativecommons.org/licenses/by/4.0/
 *
*/
/*jshint esversion: 6 */
if ( !window._brayworth_ )
	window._brayworth_ = function() { return ( window._brayworth_); };

$.extend( _brayworth_, {
		_brayworth_ : true,
		currentUser : false,
		logon_retrieve_password : false,
		templates : {},
		bootstrap_version : function() {
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

		},
		url : function( _url) {
			if ( 'undefined' == typeof _url)
				_url = '';

			return ( '/' + _url);

		},
		moment : function( a,b,c,d) {
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

		}

	});

_brayworth_.urlwrite = _brayworth_.url;	// legacy