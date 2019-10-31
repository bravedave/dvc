/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

*/
_brayworth_.initDatePickers = function( parent) {
	if ( $.fn.datepicker ) {
		if ( !parent)
			parent = 'body';

		$('.datepicker', parent).each( function( i, el ) {
			var bootstrap = (typeof $().scrollspy == 'function');
			var df = $(el).data('dateformat');
			if ( df == undefined ) {
				if ( bootstrap)
					df = 'yyyy-mm-dd';
				else if (jQuery.ui)
					df = 'yy-mm-dd';

			}

			// test if you have bootstrap
			if ( bootstrap)
				$(el).datepicker({ format : df });

			else if (jQuery.ui)
				$(el).datepicker({ dateFormat : df });


		});

	}

};

