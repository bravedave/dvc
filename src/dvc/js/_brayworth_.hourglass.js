/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */
/*jshint esversion: 6 */
var hourglass = _brayworth_.hourglass = {
	h : false,

	on  : function( msg) {
		let _me = this;

		if ( _me.h) _me.off();

		return new Promise( (resolve, reject) => {
      let inner = $('<i class="spinner-border text-white" style="position: fixed; top: 50%; left: 48%; width: 3rem; height: 3rem;"></i>');
			if (!!msg) {
				inner = $('<h1 class="p-4 text-white text-center" style="position: fixed; top: 50%; width: 100%"></h1>')
					.html( msg)
          .append('<i class="spinner-grow text-white ml-2 mb-2"></i>');

			}

			_me.h = $('<div class="modal"></div>')
				.append( inner)
				.appendTo( 'body')
				.css('display', 'block');

			resolve (_me);

		})

	},

	off : function() {
		let _me = this;

		return new Promise((resolve, reject) => {
			if ( _me.h ) _me.h.remove();	// vaporised
			_me.h = false;
			resolve(_me);

		});

	}

};
