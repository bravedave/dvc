/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	This is similar to bootstraps modal in construction,
	but also has similarity to jquery-ui functionality

	load:
		$('<script />').attr('src','/js/_brayworth_.modal.js').appendTo('head');

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
			width : 300,
			title : 'fred',
			text : 'hey jude',
			buttons : {
				Close : function(e) {
					$(this).modal( 'close');

				}

			}

		});

*/

_brayworth_.modal = function( params) {
	jQuery.fn.modal = _brayworth_.modal;	// to be sure, bootstrap has it's own modal

	if ( 'string' == typeof params) {
		/* This is a command - jquery-ui style */
		var _m = $(this).data( 'modal');
		if ( 'close' == params)
			_m.close();

		return;

	}

	var options = {
		title : '',
		width : false,
		mobile : _brayworth_.browser.isMobileDevice,
		fullScreen : _brayworth_.browser.isMobileDevice,
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
		t.get().addClass( options.className);
	t.get('.close').addClass( options.closeIcon);

	if ( !!options.width)
		t.get('.modal-content').width( options.width );
	else
		t.get('.modal-content').addClass( _brayworth_.templates.modalDefaultClass);

	var content = ( !!options.text ? options.text : '');
	if ( 'undefined' != typeof this) {
		if ( !this._brayworth_ ) {
			var content = ( this instanceof jQuery ? this : $(this));
			if ( options.title == '' && ( 'string' == typeof content.attr('title')))
				options.title = content.attr('title');

		}

	}

	t.html('H1','').append( options.title);	// jquery-ui style

	t.append( content);		// this is the content

	if ( Object.keys( options.buttons).length > 0) {	// jquery-ui style
		$.each( options.buttons, function( i, el) {
			var j = {
				text : i,
				click : function( e) {}
			}

			if ( 'function' == typeof el)
				j.click = el;
			else
				$.extend( j, el) ;

			$('<button />')
				.addClass( _brayworth_.templates.buttonCSS)
				.html( j.text)
				.on( 'click', function( e) {
					j.click.call( t.get(), e);

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
				click : function( e) {},
			}

			if ( 'function' == typeof el)
				j.click = el;
			else
				$.extend( j, el);

			if ( !!j.icon)
				var b = $( '<i class="fa fa-fw pull-right" style="margin-right: 1rem; padding-right: 1rem; cursor: pointer;" />').addClass( j.icon);

			else
				var b = $('<button class="pull-right" />')
					.html( j.text)
					.addClass( _brayworth_.templates.buttonCSS);

			if ( !!j.title)
				b.attr( 'title', j.title)

			b.on( 'click', function( e) { j.click.call( t.get(), e); })	// wrap the call and call it against the modal
			t.header.prepend( b);

		})

		t.header.prepend( $('.close', t.header));

	}

	var previousElement = document.activeElement;

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

		t.get('.modal-content').css({ 'width' : 'auto', 'margin' : 0 });

	}

	t.appendTo( 'body');

	var _modal = _brayworth_.modalDialog.call( t.get(), {
		mobile : options.mobile,
		onOpen : options.onOpen,
		afterClose : function() {
			t.get().remove();
			if ( !!options.afterClose && 'function' == typeof options.afterClose)
				options.afterClose.call( t.modal);

			/* re-activate the body elements */
			$.each( bodyElements, function( i, el){
				$(el).removeClass('hidden');

			})

			previousElement.focus();

		},

	});

	_modal.load = function( url, data, complete) {
		/*
		 * this is a wrapper on the modal->body element
		 * for jQuery.load
		 */
	 	return new Promise( function( resolve, reject) {
			var d = $('<div />');
			t.append( d);
			d.load( url, function( data) {
				resolve( data);

			});

		})

	}

	t.data( 'modal', _modal);
	if ( 'undefined' != typeof this && !this._brayworth_ ) {
		if ( this instanceof jQuery)
			this.data('modal', _modal);
		else
			$(this).data('modal', _modal);

	}

	return ( t.data( 'modal'));	// the modal

}

_brayworth_.templates.buttonCSS = 'btn btn-default';
_brayworth_.templates.modalDefaultClass = '';
_brayworth_.templates.modal = function() {
	var _ = templation.template('modal');
		_.header = _.get( '.modal-header');
		_.body = _.get( '.modal-body');
		_.append = function( p) {
			this.body.append( p);
			return ( this);

		}

		_.footer = function() {
			if ( !this._footer) {
				this._footer = $('<div class="modal-footer text-right" />');
				this.get('.modal-content').append( this._footer);

			}

			return ( this._footer);

		};

	return ( _ );

}
