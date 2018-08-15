/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/
_brayworth_.post = function( params) {
	var options = {
		url : _brayworth_.url(),
		type : 'POST',
		data : {},
		growl : function( d) {
			$('body').growlAjax( d);

		},

	};

	$.extend( options, params);
	return $.ajax(options);

}
