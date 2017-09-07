/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

*/

if ( 'undefined' == typeof _brayworth_ )
	var _brayworth_ = {
		_brayworth_ : true,
		templates : {},
		.urlwrite = function( _url) {
			if ( typeof _url == 'undefined')
				_url = '';

			return ( '/' + _url);

		}

	};
