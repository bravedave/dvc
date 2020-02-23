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
_brayworth_.InitHRefs = function() {
	$('[data-href]').each( function( i, el ) {
		$(el).css({'cursor':'pointer'}).off('click').on('click', function( e) {
			if ( /^(a)$/i.test( e.target.nodeName ))
				return;

			e.stopPropagation(); e.preventDefault();

			if ( $(e.target).closest( '[data-role="contextmenu"]' ).length > 0 )
				_brayworth_.hideContext( $(e.target).closest( '[data-role="contextmenu"]' )[0]);

			let target = $(this).data('target');
			if ( target == '' || target == undefined )
				window.location.href = $(this).data('href');

			else
				window.open( $(this).data('href'), target);

		});

	});

};