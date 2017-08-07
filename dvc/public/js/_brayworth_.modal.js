/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	This is similar to bootstraps modal in construction,
	but also has similarity to jquery-ui functionality

	Test:
		_brayworth_.modal.call( $('<div title="fred">hey jude</div>'))
		_brayworth_.modal.call( $('<div title="fred">hey jude</div>'), {
			buttons : {
				Close : function(e) {
					$(this).modal( 'close');

				}

			}

		})

		_brayworth_.modal({ title : 'fred', text : 'hey jude'});
		_brayworth_.modal({
			title : 'fred',
			text : 'hey jude',
			buttons : {
				Close : function(e) {
					$(this).modal( 'close');

				}

			}

		});

*/

_brayworth_.modal = function( params ) {
	if ( /string/.test( typeof params)) {
		/*
		 * This is a command - jquery-ui style */
		var _m = $(this).data( 'modal');
		if ( 'close' == params)
			_m.close();

		return;

	}

	var options = {
		title : '',
		width : false,
		fullScreen : _brayworth_.browser.isIPhone,
		className :  '',
		autoOpen : true,
		buttons : {},
		headButtons : {},
		closeIcon : 'fa-times',
		onOpen : function() {},

	}

	$.extend( options, params);

	var t = _brayworth_.templates.modal();
	if ( options.className != '')
		t.modal.addClass( options.className);
	t.close.addClass( options.closeIcon);

	if ( !!options.width)
		t.wrapper.width( options.width );
	else
		t.wrapper.addClass( _brayworth_.templates.modalDefaultClass);

	var content = ( !!options.text ? options.text : '');
	if ( typeof this != 'undefined') {
		if ( !this._brayworth_ ) {
			var content = ( this instanceof jQuery ? this : $(this));
			if ( options.title == '' && ( typeof content.attr('title') == 'string'))
				options.title = content.attr('title');

			//~ console.log( this, _el, 'title', options.title);

		}

	}

	t.append( content);	/* this is the content */

	t.H1.html('').append( options.title);	// jquery-ui style

	if ( Object.keys( options.buttons).length > 0) {	// jquery-ui style
		$.each( options.buttons, function( i, el) {
			var j = {
				text : i,
				click : function( e) {}
			}

			if ( /function/.test(el))
				j.click = el;

			else
				$.extend( j, el) ;

			$('<button></button>')
				.addClass( _brayworth_.templates.buttonCSS)
				.html( j.text)
				.on( 'click', function( e) {
					j.click.call( t.modal, e);

				})
				.appendTo( t.footer());

		})

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
				var b = $( '<i class="fa fa-fw pull-right" style="margin-right: 3px; padding-right: 12px; cursor: pointer;"></i>').addClass( j.icon);

			else
				var b = $('<button class="pull-right"></button>')
					.html( j.text)
					.addClass( _brayworth_.templates.buttonCSS);

			if ( !!j.title)
				b.attr( 'title', j.title)

			b.on( 'click', function( e) { j.click.call( t.modal, e); })	// wrap the call an call it against the modal
			t.header.prepend( b);

		})

		t.header.prepend( $('.close', t.header));

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

		t.wrapper.css({ 'width' : 'auto', 'margin' : 0 });

	}

	var previousElement = document.activeElement;

	t.appendTo( 'body');

	t.modal.data( 'modal', _brayworth_.modalDialog.call( t.modal, {
		onOpen : options.onOpen,
		afterClose : function() {
			t.modal.remove();
			if ( !!options.afterClose && /function/.test( typeof options.afterClose))
				options.afterClose.call( t.modal);

			/* re-activate the body elements */
			$.each( bodyElements, function( i, el){
				var _el = $(el);
				_el.removeClass('hidden');

			})

			previousElement.focus();

		},

	}));

	return ( t.modal.data( 'modal'));	// the modal

}

_brayworth_.templates.buttonCSS = 'btn btn-default';
_brayworth_.templates.modalDefaultClass = '';
_brayworth_.templates.modal = function() {

	var _ = ( function( $) {

		$.fn.modal = _brayworth_.modal;	// to be sure, bootstrap has it's own modal

		return {
			modal : $('<div class="modal"></div>'),
			wrapper : $('<div class="modal-content" role="dialog" aria-labelledby="modal-header-title"></div>'),
			header : $('<div class="modal-header"></div>'),
			close : $('<i class="fa close"></i>'),
			H1 : $('<h1 id="modal-header-title"></h1>'),
			body : $('<div class="modal-body"></div>'),
			footer : function() {
				if ( !this._footer) {
					this._footer = $('<div class="modal-footer text-right"></div>');
					this.wrapper.append( this._footer);

				}

				return ( this._footer);

			},
			append( el) {
				this.body.append( el);

			},
			appendTo( el) {
				this.modal.appendTo( el);

			},

		}

	})( jQuery);

	_.wrapper.appendTo( _.modal);
	_.close.appendTo( _.header);
	_.header.appendTo( _.wrapper);
	_.H1.appendTo( _.header);
	_.body.appendTo( _.wrapper);

	return _;

}
