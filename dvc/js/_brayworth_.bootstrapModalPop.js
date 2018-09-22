/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

*/
_brayworth_.bootstrapModalPop = function( params ) {
	if ( /string/.test( typeof params)) {
		if ( /close/i.test( params)) {
			let _modal = $(this).data( 'modal');
			_modal.close();
			return;

		}

	}

	let options = {
		title : '',
		width : false,
		autoOpen : true,
		buttons : {},
		headButtons : {},
	}

	$.extend( options, params);

	let header = $('<div class="modal-header"><i class="fa fa-times close" /><h1></h1></div>');
	let body = $('<div class="modal-body" />');
		body.append( this);
	let footer = $('<div class="modal-footer text-right" />');
	let modal = $('<div class="modal" />');

	/*---[wrapper]---*/
	let wrapper = $('<div class="modal-content" />');
		if ( options.width)
			wrapper.css({ 'width' : '300px' });
		else
			wrapper.addClass('modal-content-600');

		wrapper
			.append( header).append( body)
			.appendTo( modal);
	/*---[end: wrapper]---*/

	let _el = $(this)
	let s = _el.attr('title');

	//~ console.log( s);
	$('h1', header).html('').append( s);

	if ( Object.keys(options.buttons).length > 0) {
		$.each( options.buttons, function( i, el) {
			let b = $('<button class="button button-raised" />');
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
				let b = $('<i class="fa fa-fw pull-right" style="margin-right: 3px; padding-right: 12px; cursor: pointer;" />').addClass( el.icon);

			else
				let b = $('<button class="button button-raised pull-right" />').html( i);

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
