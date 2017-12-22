/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/
_brayworth_.Vue = function( params) {
	var options = {
		filters: {}
	};

	var filters = {
		capitalize: function (value) {
			if (!value) return ''
			value = value.toString()
			var words = value.split(' ');
			var ret = [];
			$.each( words, function( i, word) {
				if ( 'string' == typeof word)
					ret.push( word.charAt(0).toUpperCase() + word.slice(1).toLowerCase());
				else
					ret.push( word);

			});

			return ret.join(' ');
			//~ return value.charAt(0).toUpperCase() + value.slice(1).toLowerCase();

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

}
;
