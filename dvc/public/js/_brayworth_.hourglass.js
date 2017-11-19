/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/
var hourglass = _brayworth_.hourglass = {
	h : false,

	on  : function() {
		if ( !this.h ) {
			this.h = $('<div class="modal" />')
				.append( '<i class="fa fa-spinner fa-pulse fa-4x" style="position: fixed; top: 50%; left: 50%" />')
				.appendTo( 'body')
				.css('display', 'block');

		}

		return (this);

	},

	off : function() {
		if ( this.h )
			this.h.remove();	// vaporised

		this.h = false;

		return (this);

	}

};
