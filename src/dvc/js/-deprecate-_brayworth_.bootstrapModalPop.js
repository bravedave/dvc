/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * This work is licensed under a Creative Commons Attribution 4.0 International Public License.
 *      http://creativecommons.org/licenses/by/4.0/
 *
 * */
/*jshint esversion: 6 */
_brayworth_.bootstrapModalPop = function( params ) {
	if ( /string/.test( typeof params)) {
		let _modal = $(this).data( '_modal');
		if ( /close/i.test( params)) {
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
	};

	$.extend( options, params);

	let header = $('<div class="modal-header"><i class="fa fa-times close"></i><h1></h1></div>');
	let body = $('<div class="modal-body"></div>');
		body.append( this);
	let footer = $('<div class="modal-footer text-right"></div>');
	let modal = $('<div class="modal"></div>');

	/*---[wrapper]---*/
	let wrapper = $('<div class="modal-content"></div>');
		if ( options.width)
			wrapper.css({ 'width' : '300px' });
		else
			wrapper.addClass('modal-content-600');

		wrapper
			.append( header).append( body)
			.appendTo( modal);
	/*---[end: wrapper]---*/

	let _el = $(this);
	let s = _el.attr('title');

	//~ console.log( s);
	$('>h1', header).html('').append( s);

	if ( Object.keys(options.buttons).length > 0) {
		$.each( options.buttons, function( i, el) {
			let b = $('<button class="button button-raised"/>');
				b.html( i);
				b.on( 'click', function( e) {
					el.click.call( modal, e);

				});

			footer.append( b);
			//~ console.log( el);

		});

		wrapper.append( footer);

	}

	if ( Object.keys(options.headButtons).length > 0) {
		$.each( options.headButtons, function( i, el) {
			let b;
			if ( !!el.icon) {
				b = $('<i class="fa fa-fw pull-right pointer mr-1 pr-3"></i>').addClass( el.icon);

			}
			else {
				b = $('<button class="button button-raised pull-right"></button>').html( i);

			}

			if ( !!el.title) {
				b.attr( 'title', el.title);

			}

			b.on( 'click', function( e) { el.click.call( modal, e); });	// wrap the call an call it against the modal
			header.prepend( b);
			//~ console.log( el);

		});

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
