/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

*/
_brayworth_.ScrollTo = function( el, params) {

	let options = {
		marginTop : 0

	};

	$.extend( options, params);

	return ( new Promise( function( resolve, reject) {
		let _el = ( el instanceof jQuery ? el : $(el));

		let t = _el.offset().top;

		let nav = $('body>nav');
		if ( nav.length ) {
			t -= ( nav.outerHeight());

		}
		else {
			let hdr = $('body>header');
			if ( hdr.length )
				t -= ( hdr.outerHeight());

		}

		t -= options.marginTop;
		t = Math.max( 20, t);

		$('html, body').animate({ scrollTop: t}, {
			duration : 800,
			complete : resolve,
			fail : reject,

		});

	}));

}

_brayworth_.hashScroll = function() {
	/** Scrolls the content into view **/
	$('a[href*="#"]:not([href="#"] , .carousel-control, .ui-tabs-anchor)').on('click', function() {
		if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
			let target = $(this.hash);
			target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
			if (target.length) {
				if ( /nav/i.test( target.prop('tagName')))
					return;

				_brayworth_.ScrollTo( target);

				return false;

			}

		}

	});

};
