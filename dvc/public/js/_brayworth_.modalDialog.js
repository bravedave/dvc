/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	test:
		_brayworth_.modalDialog.call( $('<div class="modal"><div class="modal-content"><div class="modal-header"><i class="fa fa-times close"></i><h1>Header</h1></div><div class="modal-body">Hello World</div></div></div>').appendTo('body'))
*/

$.fn.modalDialog = _brayworth_.modalDialog = function ( _options) {
	if ( /string/.test( typeof( _options))) {
		if ( _options == 'close') {
			var modal = this.data( 'modal');
			modal.close();
			return (modal);	// chain

		}

	}

	var modal = this;				// the modal
	var options = {
		mobile : _brayworth_.browser.isMobileDevice,
		beforeClose : function() {},
		afterClose : function() {},
		onEnter : function() {},
		onOpen : function() {},
	};

	$.extend( options, _options);

	var close = $( '.close', this);	// Get the <span> element that closes the modal

	modal.close = function() {
		options.beforeClose.call( modal);
		modal.css( 'display', 'none');
		//~ $(window).off('click');
		options.afterClose.call( modal);

		modal = false;
		$(document).unbind('keyup.modal');
		$(document).unbind('keypress.modal');

	}

	if ( options.mobile)
		modal.addClass( 'modal-mobile');
	modal.css( 'display', 'block').data('modal', modal);

	var _AF = $('[autofocus]',modal);
	if ( _AF.length > 0) {
		_AF.first().focus();

	}
	else {
		_AF = $('textarea, input, button',modal);
		if ( _AF.length > 0)
			_AF.first().focus();

	}

	$(document)
	.on( 'keyup.modal', function( e) {
		if (e.keyCode == 27) {
			// escape key maps to keycode `27`
			if ( modal)
				modal.close();

		}

	})
	.on( 'keypress.modal', function( e) {
		if (e.keyCode == 13)
			options.onEnter.call( modal, e);

	})

	close
	.off('click')
	.css({cursor:'pointer'})
	.on('click', function(e) { modal.close(); });	// When the user clicks on <span> (x), close the modal

	options.onOpen.call( modal);

	return ( modal);	// chain

}
