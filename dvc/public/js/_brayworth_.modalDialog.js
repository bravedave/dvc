/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	test:
		_brayworth_.modalDialog.call( $('<div class="modal"><div class="modal-content"><div class="modal-header"><i class="fa fa-times close"></i><h1>Header</h1></div><div class="modal-body">Hello World</div></div></div>').appendTo('body'))
		_brayworth_.modalDialog.call( $('<div class="modal"><div class="modal-dialog"><div class="modal-content"><div class="modal-header py-1"><h5 class="modal-title">Hello World</h5><i class="fa fa-times close" /></div><div class="modal-body">...</div></div></div></div>').appendTo('body'))
*/

$.fn.modalDialog = _brayworth_.modalDialog = function ( _options) {
	if ( /string/.test( typeof( _options))) {
		if ( _options == 'close') {
			let modal = this.data( 'modal');
			modal.close();
			return (modal);	// chain

		}

	}

	let modal = this;				// the modal
	let options = {
		mobile : _brayworth_.browser.isMobileDevice,
		beforeClose : function() {},
		afterClose : function() {},
		onEnter : function() {},
		onEscape : function() { this.close(); },
		onOpen : function() {},
	};

	$.extend( options, _options);

	let close = $( '.modal-header .close', this);	// Get the <span> element that closes the modal

	modal.close = function() {
		options.beforeClose.call( modal);
		modal.removeClass( 'modal-active');
		$('body').removeClass( 'modal-open').css('padding-right', '');	// credit bootstrap class
		//~ $(window).off('click');
		options.afterClose.call( modal);

		modal = false;
		$(document).unbind('keyup.modal');
		$(document).unbind('keypress.modal');

	}

	$('body').addClass( 'modal-open');	// bootstrap class

	if ( options.mobile) {
		modal.addClass( 'modal-mobile');

	}
	else {
		(function() {
			let rect = document.body.getBoundingClientRect();
			if (document.body.scrollHeight > window.innerHeight) {
				$('body').css('padding-right', '17px');	// credit bootstrap

			}

		})();

	}

	modal.addClass( 'modal-active').data('modal', modal);

	let _AF = $('[autofocus]',modal);
	if ( _AF.length > 0) {
		_AF.first().focus();

	}
	else {
		_AF = $('textarea:not([readonly]), input:not([readonly]), button:not([disabled]), a:not([tabindex="0"])',modal);
		if ( _AF.length > 0)
			_AF.first().focus();

	}

	$(document)
	.on( 'keyup.modal', function( e) {
		if (e.keyCode == 27) {
			// escape key maps to keycode `27`
			if ( modal) {
				options.onEscape.call( modal, e);

			}

		}

	})
	.on( 'keypress.modal', function( e) {
		if (e.keyCode == 13) {
			options.onEnter.call( modal, e);

		}

	});

	// When the user clicks on <span> (x), close the modal
	close.off('click').addClass('pointer').on('click', function(e) { modal.close(); });

	options.onOpen.call( modal);

	return ( modal);	// chain

}
