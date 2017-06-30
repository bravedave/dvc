/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	Test:
		_brayworth_.modal.call( $('<div title="fred">hey jude</div>'))

*/
if ( typeof _brayworth_ == 'undefined')
	var _brayworth_ = {};

_brayworth_.modal = function( params ) {
	if ( /string/.test( typeof params)) {
		/*
		 * This is a command - jquery-ui style */
		var modal = $(this).data( 'modal');
		if ( 'close' == params)
			modal.close();

		return;

	}

	var options = {
		title : '',
		width : false,
		fullScreen : _brayworth_.browser.isIPhone,
		autoOpen : true,
		buttons : {},
		headButtons : {},

	}

	$.extend( options, params);

	var modal = $('<div class="modal"></div>');
	var wrapper = $('<div class="modal-content" role="dialog" aria-labelledby="modal-header-title"></div>').appendTo( modal);

	var header = $('<div class="modal-header"><i class="fa fa-times close"></i></div>').appendTo( wrapper);
	var headerH1 = $('<h1 id="modal-header-title"></h1>').appendTo( header);
	var body = $('<div class="modal-body"></div>').append( this).appendTo( wrapper);

	var footer = $('<div class="modal-footer text-right"></div>');

	if ( !!options.width)
		wrapper.width( options.width );
	//~ else
		//~ wrapper.addClass('modal-content-600');

	var _el = ( this instanceof jQuery ? this : $(this));
	var s = _el.attr('title');
		//~ console.log( this, _el, 'title',s);

	headerH1.html('').append( s);	// jquery-ui style

	if ( Object.keys(options.buttons).length > 0) {	// jquery-ui style
		$.each( options.buttons, function( i, el) {
			var j = {
				text : i,
				click : function( e) {}
			}

			if ( /function/.test(el))
				j.click = el;

			else
				$.extend( j, el) ;

			var b = $('<button class="button button-raised"></button>')
				b.html( j.text);
				b.on( 'click', function( e) {
					j.click.call( modal, e);

				})

			footer.append( b);
			//~ console.log( el);

		})

		wrapper.append( footer);

	}

	if ( Object.keys(options.headButtons).length > 0) {
		$.each( options.headButtons, function( i, el) {
			var j = {
				text : i,
				title : false,
				icon : false,
				click : function( e) {}
			}

			if ( /function/.test(el))
				j.click = el;

			else
				$.extend( j, el);

			if ( !!j.icon)
				var b = $('<i class="fa fa-fw pull-right" style="margin-right: 3px; padding-right: 12px; cursor: pointer;"></i>').addClass( j.icon);

			else
				var b = $('<button class="button button-raised pull-right"></button>').html( j.text);

			if ( !!j.title)
				b.attr( 'title', j.title)

			b.on( 'click', function( e) { j.click.call( modal, e); })	// wrap the call an call it against the modal
			header.prepend( b);

		})

		header.prepend( $('.close', header));

	}

	var bodyElements = [];
	if ( options.fullScreen) {
		/* hide all the body elements */
		$('body > *').each( function( i, el){
			var _el = $(el);
			if ( !_el.hasClass('hidden')) {
				_el.addClass('hidden');
				bodyElements.push( _el);

			}

		})

		wrapper.css({ 'width' : 'auto', 'margin' : 0 });

	}

	var previousElement = document.activeElement;

	modal.appendTo( 'body');

	$(this).data('modal', _brayworth_.modalDialog.call(modal, {
		afterClose : function() {
			modal.remove();
			if ( !!options.afterClose && /function/.test( typeof options.afterClose))
				options.afterClose.call( modal);

			/* re-activate the body elements */
			$.each( bodyElements, function( i, el){
				var _el = $(el);
				_el.removeClass('hidden');

			})

			previousElement.focus();

		},

	}));

}
