/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */
/*jshint esversion: 6 */
(($, _) => {
	$(document).ready(() => {
		$('[data-role="back-button"]').each((i, el) => {
			//~ console.log('processing : window.history.back()');
			$(el)
				.css('cursor', 'pointer')
				.on('click', e => {
					e.stopPropagation(); e.preventDefault();
					//~ console.log('window.history.back()');
					window.history.back();

				});

		});

		$('[data-role="visibility-toggle"]').each((i, el) => {
			let _el = $(el);
			let target = _el.data('target');
			let oT = $('#' + target);
			if (oT) {
				_el
					.css('cursor', 'pointer')
					.on('click', e => {
						e.stopPropagation(); e.preventDefault();
						oT.toggle();

					});

			}

		});

		$('[role="print-page"]').each((i, el) => {
			$(el).on('click', e => {
				e.preventDefault();
				window.print();

			});

		});

	});

	$.fn.serializeFormJSON = function () {

		/**
		 * thinking this should be deprecated to
		 * use the formDataToJson function
		 */

		let o = {};
		let a = this.serializeArray();
		$.each(a, (i, el) => {

			if (o[el.name]) {

				if (!o[el.name].push) {

					o[el.name] = [o[el.name]];
				}

				o[el.name].push(el.value || '');
			} else {

				o[el.name] = el.value || '';
			}
		});

		return o;
	};

	$.fn.zIndex = function (z) {
		if (/number|string/.test(typeof z)) {
			return (this.css('z-index', z));	// consistent

		}
		else {
			// otherwise the calculated value
			z = window.document.defaultView.getComputedStyle(this[0]).getPropertyValue('z-index');
			if (isNaN(z))
				z = 0;

			z = parseInt(z);
			$.each(this.parents(), function (i, el) {
				let _z = window.document.defaultView.getComputedStyle(el).getPropertyValue('z-index');
				if (!isNaN(_z))
					z += parseInt(_z);

			});

			return z;

		}

	};

})(jQuery, _brayworth_);
