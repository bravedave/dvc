/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

*/
_brayworth_.InitHRefs = function() {
	$('[data-href]').each( function( i, el ) {
		$(el)
		.css('cursor','pointer')
		.off('click')
		.on('click', function( evt ) {
			if ( /^(a)$/i.test( evt.target.nodeName ))
				return;

			evt.stopPropagation(); evt.preventDefault();

			if ( $(evt.target).closest( '[data-role="contextmenu"]' ).length > 0 )
				_brayworth_.hideContext( $(evt.target).closest( '[data-role="contextmenu"]' )[0]);

			var target = $(this).data('target');
			if ( target == '' || target == undefined )
				window.location.href = $(this).data('href');

			else
				window.open( $(this).data('href'), target);


		})

	})

};