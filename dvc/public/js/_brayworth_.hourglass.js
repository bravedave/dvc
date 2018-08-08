/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/
var hourglass = _brayworth_.hourglass = {
	h : false,

	on  : function( msg) {
		if ( this.h) this.off();

		let inner = $('<i class="fa fa-fa-circle-o-notch fa-pulse fa-4x text-white" style="position: fixed; top: 50%; left: 48%" />');
		if (!!msg) {
			inner = $('<h1 class="p-4 text-white text-center" style="position: fixed; top: 50%; width: 100%" />')
				.html( msg)
				.append('<i class="fa fa-fw fa-spinner fa-pulse text-white ml-2" />');

		}

		this.h = $('<div class="modal" />')
			.append( inner)
			.appendTo( 'body')
			.css('display', 'block');

		return (this);

	},

	off : function() {
		if ( this.h )
			this.h.remove();	// vaporised

		this.h = false;

		return (this);

	}

};
