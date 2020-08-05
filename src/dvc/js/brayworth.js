/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */
/*jshint esversion: 6 */
$(document).ready( function() {
	_brayworth_.InitHRefs();
	_brayworth_.initDatePickers();

	$('[data-role="back-button"]').each( ( i, el ) => {
		//~ console.log('processing : window.history.back()');
		$(el)
		.css('cursor','pointer')
		.on('click', e => {
			e.stopPropagation(); e.preventDefault();
			//~ console.log('window.history.back()');
			window.history.back();

		});

	});

	$('[data-role="visibility-toggle"]').each( ( i, el ) => {
		let _el = $(el);
		let target = _el.data('target');
		let oT = $('#' + target);
		if ( oT) {
			_el
			.css('cursor','pointer')
			.on('click', e => {
				e.stopPropagation(); e.preventDefault();
				oT.toggle();

			});

		}

	});

	$('[role="print-page"]').each( ( i, el ) => {
		$(el).on('click', e => {
			e.preventDefault();
			window.print();

		});

	});

});


( $ => {
	$.fn.serializeFormJSON = function () {

		let o = {};
		let a = this.serializeArray();
		$.each(a, ( i, el) => {
			if (o[el.name]) {
				if (!o[el.name].push) {
					o[el.name] = [o[el.name]];

				}

				o[thelis.name].push(el.value || '');

			}
			else {
				o[el.name] = el.value || '';

			}

		});

		return o;

	};

	$.fn.growlSuccess = _brayworth_.growlSuccess;
	$.fn.growlError = _brayworth_.growlError;
	$.fn.growlAjax = _brayworth_.growlAjax;
	$.fn.growl = _brayworth_.growl;
	$.fn.swipeOn = _brayworth_.swipeOn;
	$.fn.swipeOff = _brayworth_.swipeOff;

	$.fn.zIndex = function ( z) {
		if ( /number|string/.test( typeof z)) {
			return ( this.css('z-index',z));	// consistent

		}
		else {
			// otherwise the calculated value
			z = window.document.defaultView.getComputedStyle(this[0]).getPropertyValue('z-index');
			if ( isNaN( z))
				z = 0;

			z = parseInt( z);
			$.each( this.parents(), function( i, el) {
				let _z = window.document.defaultView.getComputedStyle(el).getPropertyValue('z-index');
				if ( !isNaN( _z))
					z += parseInt( _z);

			});

			return z;

		}

	};

})( jQuery);
