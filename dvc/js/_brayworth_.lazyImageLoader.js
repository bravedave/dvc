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
_brayworth_.lazyImageLoader = function() {
	let imgStack = [];

	$('div[data-delayedimg="true"]').each( function( i, el) {
		let _ = $(el);
		if ( _.visible( true))
			_
			.css({'background-image' : 'url("' + _.data('src') + '")'})
			.data('delayedimg', false);

		else
			imgStack.push( _);

	});

	if ( imgStack.length > 0) {
		//~ console.log( 'unloaded images', imgStack.length);

		$(document).on('scroll', function( e) {
			let unProcessed = 0;
			$.each( imgStack, function( i, el) {
				let _ = $(el);
				if ( _.data('delayedimg')) {
					if ( _.visible( true)) {
						//~ console.log( 'loading', _.data('src'));
						_
						.css({ 'background-image' : 'url("' + _.data('src') + '")'})
						.data('delayedimg', false);

					}
					else {
						unProcessed ++;

					}

				}

			});

			//~ console.log( 'checking unloaded images', unProcessed);
			if ( unProcessed < 1) {
				$(document).off( 'scroll');
				//~ console.log( 'disabled scroll check');

			}

		});

	}

};
