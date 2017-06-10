/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

*/

if ( typeof _brayworth_ == 'undefined')
	var _brayworth_ = {};

_brayworth_.hideContext = function( el) {
	var _el = $(el);
	if ( !!_el.data('hide')) {
		if ( _el.data('hide') == 'hide')
			$(el).addClass('hidden');	// connotes there is a hidden class
		else
			$(el).remove();

	}
	else {
		$(el).remove();

	}

}

_brayworth_.hideContexts = function() {
	$('[data-role="contextmenu"]').each( function( i, el ) { _brayworth_.hideContext( el); });

}

_brayworth_.context = function() {
	return ({
		root : $('<ul class="menu menu-contextmenu" data-role="contextmenu"></ul>'),
		detachOnHide : true,

		create : function( item) {
			return ( $( '<li></li>').append( item).appendTo( this.root));

		},

		append : function( item) {
			this.create( item);
			return ( this);

		},

		open : function( evt) {
			var css = {
				position: 'absolute',
				top : 10,
				left : $(document).width() - 140,
				}

			if ( !!evt.pageY)
				css.top = Math.max( evt.pageY - 10, 0);
			if ( !!evt.pageX)
				css.left = Math.max( evt.pageX - 40, 0);

			//~ console.log( this.root.width());

			if ( this.detachOnHide) {
				this.root.css(css).appendTo( 'body').data('hide', 'detach');

			}
			else {
				//~ console.log( this.root.parent());
				if ( this.root.parent().length < 1)
					this.root.appendTo( 'body').data('hide', 'hide');
				this.root.css(css).removeClass('hidden');

			}

			var offset = this.root.offset();
			/* try to keep menu on screen horizontally */
			if ( offset.left + this.root.width() > $(window).width()) {
				//~ console.log( 'uh oh - right!');
				var l = $(window).width()-this.root.width()-5;
				this.root.css( 'left', Math.max( l, 2));
				offset = this.root.offset();

			}

			/* try to keep menu on screen vertically */
			if ( offset.top + this.root.height() > $(window).height()) {
				//~ console.log( 'uh oh - top!');
				var t = $(window).height()-this.root.height()-5;
				this.root.css( 'top', Math.max( t, 2));
				offset = this.root.offset();

			}

			/* add helper class to display the menu on left	*
			 * if the window width is restrictive on the right	*/
			if ( offset.left > ( $(window).width() - (this.root.width()* 2)))
				this.root.addClass( 'menu-contextmenu-right');
			else
				this.root.removeClass( 'menu-contextmenu-right');

			//~ console.log( offset.left, $(window).width() * .8);
			//~ console.log( this.root.width());

			return ( this);

		},

		close : function() {
			if ( this.detachOnHide) {
				this.root.remove();
				//~ console.log( 'removed context menu');

			}
			else {
				this.root.addClass('hidden');	// connotes there is a hidden class
				//~ console.log( 'hide context menu');

			}

			return ( this);

		},

		remove : function() {
			return ( this.close());

		},

		attachTo : function( parent) {

			var _me = this;

			$( parent)
			.off( 'click.removeContexts')
			.on( 'click.removeContexts', function( evt) {
				if ( $(evt.target).closest( '[data-role="contextmenu"]' ).length > 0 ) {
					if ( /^(a)$/i.test( evt.target.nodeName ))
						return;

				}

				_brayworth_.hideContexts();

			})
			.on( 'contextmenu', function( evt) {
				/*--[ check for abandonment ]--*/
				if( $(evt.target).closest('[data-role="contextmenu"]').length)
					return;

				_brayworth_.hideContexts();

				if ( evt.shiftKey)
					return;

				if ( /^(input|textarea|img|a|select)$/i.test( evt.target.nodeName ))
					return;

				if ( $(evt.target).closest('table').data('nocontextmenu') == 'yes' )
					return;

				if ( $(evt.target).hasClass('modal' ) || $(evt.target).closest('.modal').length > 0)
					return;

				//~ console.log( evt.target);

				if (typeof window.getSelection != "undefined") {
					var sel = window.getSelection();
					if (sel.rangeCount) {
						if ( sel.anchorNode.parentNode == evt.target ) {
							var frag = sel.getRangeAt(0).cloneContents();
							var text = frag.textContent;
							if ( text.length > 0 )
								return;

						}

					}

				}
				/*--[ end: check for abandonment ]--*/

				evt.preventDefault();
				_me.open( evt);

			});

			return ( _me);

		}

	})

};
