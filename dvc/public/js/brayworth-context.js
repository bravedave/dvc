/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

*/

if (!_brayworth_)
	_brayworth_ = {};

_brayworth_.context = function() {
	return ({
		root : $('<ul class="menu contextmenu"></ul>'),
		detachOnHide : true,

		append : function( item) {
			$( '<li></li>').append( item).appendTo( this.root);
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

			//~ console.log( evt, css);

			if ( this.detachOnHide) {
				this.root.css(css).appendTo( 'body').data('hide', 'detach');

			}
			else {
				//~ console.log( this.root.parent());
				if ( this.root.parent().length < 1)
					this.root.css(css).appendTo( 'body').data('hide', 'hide');
				this.root.removeClass('hidden');

			}

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

		hideContexts : function() {
			$('.contextmenu').each( function( i, el ) {
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

			});

		},

		attachTo : function( parent) {

			var _me = this;

			$( parent)
			.off( 'click.removeContexts')
			.on( 'click.removeContexts', function( evt) {
				if ( $(evt.target).closest( '.contextmenu' ).length > 0 ) {
					if ( /^(a)$/i.test( evt.target.nodeName ))
						return;

				}

				_me.hideContexts();

			})
			.on( 'contextmenu', function( evt) {
				/*--[ check for abandonment ]--*/
				if( $(evt.target).closest('.contextmenu').length)
					return;

				_me.hideContexts();

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
