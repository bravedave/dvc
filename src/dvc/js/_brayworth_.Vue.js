/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/
/*jshint esversion: 6 */
_brayworth_.Vue = function( params) {
	let options = {
		filters: {}
	};

	let filters = {
		capitalize: function (value) {
			if (!value) return '';
			return value.toCapitalCase();

		}

	};

	$.extend( options, params);
	$.extend( options.filters, filters);

	return new Promise( function( resolve, reject) {
		if ( 'undefined' === typeof Vue) {
			_cms_.getScript( _brayworth_.urlwrite( 'js/vue.min.js')).then( function(d) {
				resolve( new Vue( options));

			});

		}
		else {
			resolve( new Vue( options));

		}

	});

};
