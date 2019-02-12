/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

*/

_brayworth_.hideContext = function( el) {
	let _el = $(el);
	if ( !!_el.data('hide')) {
		if ( _el.data('hide') == 'hide') {
			if ( _brayworth_.bootstrap_version() >= 4) {
				$(el).addClass('d-none');	// connotes there is a hidden class

			}
			else {
				$(el).addClass('hidden');	// connotes there is a hidden class

			}

		}
		else {
			$(el).remove();

		}

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
		root : $('<ul class="menu menu-contextmenu" data-role="contextmenu" />'),
		items : [],
		length : 0,
		detachOnHide : true,
		hideClass : ( _brayworth_.bootstrap_version() < 4 ? 'hidden' : 'd-none'),

		create : function( item) {
			let el = $( '<li />').append( item).appendTo( this.root);
			this.items.push( el);
			this.length = this.items.length;
			return ( el);

		},

		append : function( item) {
			this.create( item);
			return ( this);

		},

		open : function( e) {
			let css = {
				position: 'absolute',
				top : 10,
				left : $(document).width() - 140,

			}

			if ( !!e.pageY) { css.top = Math.max( e.pageY + 2, 0); }
			if ( !!e.pageX) { css.left = Math.max( e.pageX + 2, 0); }

			//~ console.log( this.root.width());

			let root = this.root;
			(function( e) {
				let target = $( e.target);
				if ( target.length > 0) {
					css['z-index'] = target.zIndex() + 10;

				}

			})( e);


			if ( this.detachOnHide) {
				root.css(css).appendTo( 'body').data('hide', 'detach');

			}
			else {
				//~ console.log( this.root.parent());
				if ( root.parent().length < 1) {
					root.appendTo( 'body').data('hide', 'hide');

				}

				root.css(css).removeClass('hidden d-none');

			}

			let offset = root.offset();
			let wH = $(window).height();
			let wW = $(window).width();
			let sT = $(window).scrollTop();
			/* try to keep menu on screen horizontally */
			if ( offset.left + root.width() > wW) {
				//~ console.log( 'uh oh - right!');
				let l = wW - root.width() - 5;
				root.css( 'left', Math.max( l, 2));
				offset = root.offset();

			}

			/* try to keep menu on screen vertically */
			if ( offset.top + this.root.height() > ( wH + sT)) {
				//~ console.log( 'uh oh - top!');
				let t = (wH + sT) - root.height() - 5;
				root.css( 'top', Math.max( t, sT + 2));
				offset = root.offset();

			}


			/* add helper class to display the submenu on left if the window width is restrictive on the right */
			( offset.left > ( wW - (root.width()* 2))) ? root.addClass( 'menu-contextmenu-right') : root.removeClass( 'menu-contextmenu-right');

			/* add helper class to display the submenu high if the window height is restrictive at bottom */
			( offset.top + ( root.height() * 1.2) > ( wH + sT)) ? root.addClass( 'menu-contextmenu-low') : root.removeClass( 'menu-contextmenu-low');

			return ( this);

		},

		close : function() {
			if ( this.detachOnHide) {
				this.root.remove();
				//~ console.log( 'removed context menu');

			}
			else {
				this.root.addClass( this.hideClass);	// connotes there is a hidden class
				//~ console.log( 'hide context menu');

			}

			return ( this);

		},

		remove : function() {
			return ( this.close());

		},

		attachTo : function( parent) {

			let _me = this;

			$( parent)
			.off( 'click.removeContexts')
			.on( 'click.removeContexts', function( e) {
				if ( $(e.target).closest( '[data-role="contextmenu"]' ).length > 0 ) {
					if ( /^(a)$/i.test( e.target.nodeName )) {
						return;

					}

				}

				_brayworth_.hideContexts();

			})
			.on( 'contextmenu', function( e) {
				/*--[ check for abandonment ]--*/
				if( $(e.target).closest('[data-role="contextmenu"]').length) {
					return;

				}

				_brayworth_.hideContexts();

				if ( e.shiftKey) {
					return;

				}

				if ( /^(input|textarea|img|a|select)$/i.test( e.target.nodeName ) || $(e.target).closest('a').length > 0) {
					return;

				}

				if ( $(e.target).closest('table').data('nocontextmenu') == 'yes' ) {
					return;

				}

				if ( $(e.target).hasClass('modal' ) || $(e.target).closest('.modal').length > 0) {
					return;

				}

				/** This stops the menu on jquery-ui dialogs */
				if ( $(e.target).hasClass('ui-widget-overlay' ) || $(e.target).closest( '.ui-dialog').length > 0) {
					return;

				}

				if (typeof window.getSelection != "undefined") {
					let sel = window.getSelection();
					if (sel.rangeCount) {
						if ( sel.anchorNode.parentNode == e.target ) {
							let frag = sel.getRangeAt(0).cloneContents();
							let text = frag.textContent;
							if ( text.length > 0 )
								return;

						}

					}

				}
				/*--[ end: check for abandonment ]--*/

				e.preventDefault();
				_me.open( e);

			});

			return ( _me);

		}

	});

};

$(document).ready( function() {
	$(document)
	.on( 'keyup.removeContexts', function( e) {
		if ( 27 == e.keyCode) {
			_brayworth_.hideContexts();

		}

	})
	.on( 'click.removeContexts', function( e) {
		if ( $(e.target).closest( '[data-role="contextmenu"]' ).length > 0 ) {
			if ( /^(a)$/i.test( e.target.nodeName )) { return; }

		}

		_brayworth_.hideContexts();

	})
	.on( 'contextmenu.removeContexts', function( e) {
		if ( $(e.target).closest( '[data-role="contextmenu"]' ).length > 0 ) {
			if ( /^(a)$/i.test( e.target.nodeName )) { return; }

		}

		_brayworth_.hideContexts();

	});

});
