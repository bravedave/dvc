/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

*/
/*jshint esversion: 6 */
_brayworth_.ScrollTo = function( el, params) {

	let options = {
		marginTop : 0,
		duration : 800

	};

	$.extend( options, params);

	return ( new Promise( function( resolve, reject) {
		let _el = ( el instanceof jQuery ? el : $(el));
		if ( _el.length < 1) {
			console.log( 'element not found', el);
			resolve();

			return;

		}

		let t = _el.offset().top;
		// console.log( _el, t);

		let parent = _el.closest('.modal');
		if ( parent.length == 0) {
			let nav = $('body>nav');
			if ( nav.length ) {
				t -= ( nav.outerHeight());

			}
			else {
				let hdr = $('body>header');
				if ( hdr.length )
					t -= ( hdr.outerHeight());

			}

		}

		t -= options.marginTop;
		t = Math.max( 20, t);

		if (parent.length > 0) {
			parent.animate({ scrollTop: t}, {
				duration : options.duration,
				complete : resolve,
				fail : reject,

			});

		}
		else {
			$('html,body').animate({ scrollTop: t}, {
				duration : options.duration,
				complete : resolve,
				fail : reject,

			});

		}

	}));

};

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
