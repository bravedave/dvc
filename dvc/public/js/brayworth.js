/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

*/
( function() {
	//~ if ( true) {
	if ( false) {
		// Add feature check for Service Workers here
		if('serviceWorker' in navigator) {
			if ( /^https:/.test( window.location.href)) {
				navigator.serviceWorker
				.register('/js/service-worker.js')
				.then(function() {
					//~ console.log('Service Worker Registered');
				});

			}
			//~ else {
				//~ console.log( 'insecure location : not loading service worker');

			//~ }

		}

	}

})();

$(document).ready( function() {
	_brayworth_.InitHRefs();
	_brayworth_.initDatePickers();

	$('[data-role="back-button"]').each( function( i, el ) {
		//~ console.log('processing : window.history.back()');
		$(el)
		.css('cursor','pointer')
		.on('click', function( evt ) {
			evt.stopPropagation(); evt.preventDefault();
			//~ console.log('window.history.back()');
			window.history.back();

		})

	})

	$('[data-role="visibility-toggle"]').each( function( i, el ) {
		//~ console.log('processing : window.history.back()');
		var o = $(el);
		var target = o.data('target');
		var oT = $('#' + target);
		if (oT) {

			o
			.css('cursor','pointer')
			.on('click', function( evt ) {
				evt.stopPropagation(); evt.preventDefault();
				if ( oT.hasClass( 'hidden'))
					oT.removeClass( 'hidden' );
				else
					oT.addClass( 'hidden' );

			})

		}

	})

	$('[role="print-page"]').each( function( i, el ) {
		$(el).on('click', function(e) {
			e.preventDefault();
			window.print();

		});

	});

});


(function ($) {
	$.fn.serializeFormJSON = function () {

		var o = {};
		var a = this.serializeArray();
		$.each(a, function () {
			if (o[this.name]) {
				if (!o[this.name].push)
					o[this.name] = [o[this.name]];

				o[this.name].push(this.value || '');
			}
			else
				o[this.name] = this.value || '';

		});

		return o;

	};

	$.fn.growlSuccess = _brayworth_.growlSuccess;
	$.fn.growlError = _brayworth_.growlError;
	$.fn.growlAjax = _brayworth_.growlAjax;
	$.fn.growl = _brayworth_.growl;
	$.fn.swipeOn = _brayworth_.swipeOn;
	$.fn.swipeOff = _brayworth_.swipeOff;

	String.prototype.isEmail = function() {
		if ( this.length < 3)
			return ( false);

		var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
		return emailReg.test(this);

	}

})( jQuery);
