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
_brayworth_.loadModal = function( params) {
	let options = $.extend({
		url : _brayworth_.url('modal'),
		headerClass : '',
		beforeOpen : function() {},
		onClose : function() {},
		onSuccess : function() {},

	}, params);

	//~ console.log( options);

	return ( new Promise( function( resolve, reject) {
		_brayworth_.get( options.url).then( function( data) {
			let modal = $(data).appendTo('body');

			modal.on('brayworth.success', options.onSuccess);
			modal.on('brayworth.modal', options.onClose);
			modal.on('hidden.bs.modal', function (e) {
				modal.remove();
				modal.trigger( 'brayworth.modal');

			});

			if ( '' != options.headerClass) {
				$('.modal-header', modal).removeClass()
					.addClass('modal-header')
					.addClass( options.headerClass);

			}

			if ( !_brayworth_.browser.isMobileDevice) {
				let mbody = $('.modal-body', modal);
				let autofocus = $('[autofocus]', mbody);
				if ( autofocus.length > 0) {
					modal.on('shown.bs.modal', function (e) {
						autofocus.first().focus();

					});

				}
				else {
					autofocus = $('textarea:not([type="hidden"]):not([readonly]), input:not([type="hidden"]):not([readonly]), button:not([disabled]), a:not([tabindex="0"])', mbody);
					if ( autofocus.length > 0) {
						modal.on('shown.bs.modal', function (e) {
							autofocus.first().focus();

						});

					}

				}

			}

			modal.on('show.bs.modal', options.beforeOpen);

			modal.modal( 'show');
			resolve( modal);

		});

	}));

};
