/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

*/

if ( typeof _brayworth_ == 'undefined')
	var _brayworth_ = {};

_brayworth_.bootstrapModalPop = function( params ) {
	if ( /string/.test( typeof params)) {
		var modal = $(this).data( 'modal');
		if ( /close/i.test( params)) {
			modal.close();
			return;

		}

	}

	var options = {
		title : '',
		width : false,
		autoOpen : true,
		buttons : {},
		headButtons : {},
	}

	$.extend( options, params);

	var header = $('<div class="modal-header"><i class="fa fa-times close"></i><h1></h1></div>');
	var body = $('<div class="modal-body"></div>');
		body.append( this);
	var footer = $('<div class="modal-footer text-right"></div>');
	var modal = $('<div class="modal"></div>');

	/*---[wrapper]---*/
	var wrapper = $('<div class="modal-content"></div>');
		if ( options.width)
			wrapper.css({ 'width' : '300px' });
		else
			wrapper.addClass('modal-content-600');

		wrapper
			.append( header).append( body)
			.appendTo( modal);
	/*---[end: wrapper]---*/

	var _el = $(this)
	var s = _el.attr('title');

	//~ console.log( s);
	$('h1', header).html('').append( s);

	if ( Object.keys(options.buttons).length > 0) {
		$.each( options.buttons, function( i, el) {
			var b = $('<button class="button button-raised"></button>')
				b.html( i);
				b.on( 'click', function( e) {
					el.click.call( modal, e);

				})

			footer.append( b);
			//~ console.log( el);

		})

		wrapper.append( footer);

	}

	if ( Object.keys(options.headButtons).length > 0) {
		$.each( options.headButtons, function( i, el) {
			if ( !!el.icon)
				var b = $('<i class="fa fa-fw pull-right" style="margin-right: 3px; padding-right: 12px; cursor: pointer;"></i>').addClass( el.icon);

			else
				var b = $('<button class="button button-raised pull-right"></button>').html( i);

			if ( !!el.title)
				b.attr( 'title', el.title)

			b.on( 'click', function( e) { el.click.call( modal, e); })	// wrap the call an call it against the modal
			header.prepend( b);
			//~ console.log( el);

		})

		header.prepend( $('.close', header));

	}

	modal.appendTo( 'body');

	$(this).data('modal', modal.modalDialog({
		afterClose : function() {
			modal.remove();
			if ( !!options.afterClose && /function/.test( typeof options.afterClose))
				options.afterClose.call( modal);

		},

	}));

};
