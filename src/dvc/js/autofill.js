/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 *	source:
 *		the source is inspired by jquery-ui and should behave as such
 *
 *		the request is passed in the format [<jsonObject>.term]
 *		the response will be parsed in the fashion [<jsonObject>.label]
 * */
/*jshint esversion: 6 */
($ => {
	$.fn.autofill = function( params) {
		var _me = $(this);

		if ( 'string' == typeof params) {
			if ( 'destroy' == params) {
				_me.off( 'keyup.autofill');
				_me.off( 'focus.autofill');
				_me.off( 'blur.autofill');
				$('.autofill-wrapper', _me).remove();

				return _me;

			}

		}

		let options = _brayworth_.extend({
			timeout : 400,
			appendTo : _me.parent(),
			wrapper : $('<div class="autofill-wrapper"></div>'),
			autoFocus : false,
			activateOnEnter : true,
			minLength : 3,
			minWidth : 150,
			select : false,
			source : function( request, response) {},

		}, params);

		//~ console.log( 'autofill');
		let list = $('<ul class="list-group" style="position: absolute; left: 0; z-index: 5; width: 100%;"></ul>');

		if ( !( options.appendTo instanceof jQuery)) {
			options.appendTo = $(options.appendTo);

		}

		options.wrapper.append( list).appendTo( options.appendTo);

		let keyMove = {
			active : -1,
			items : function() {
				return ( $('>li', list));

			},

			current : false,
			activate : function( item ) {
				if ( this.current) {
					this.current.removeClass( 'active');
					this.current = false;

				}

				this.current = $( item);
				this.current.addClass('active');

			},

			deactivate : function( item ) {

				var _item = $(item);
				if ( _item.hasClass('active')) {
					_item.removeClass( 'active');

				}

				if ( this.current && !this.current.hasClass('active')) {
					/* there is no item active */
					this.current = false;

				}

			},

			up : function() {
				var items = this.items();
				if ( items.length > 0) {
					var item = -1;
					$.each( items, function( i, el) {
						if ( $(el).hasClass('active')) {
							item = i;
							return ( true);

						}

					});

					if ( item < 0) {
						this.activate( items[items.length -1]);

					}
					else {
						//~ $(items[item]).removeClass('active');
						item --;
						if ( item < 0)
							this.activate( items[items.length -1]);
						else
							this.activate( items[item]);

					}

					//~ console.log( 'up');

				}

			},

			down : function() {
				let items = this.items();
				if ( items.length > 0) {
					var item = -1;
					$.each( items, function( i, el) {
						if ( $(el).hasClass('active')) {
							item = i;
							return ( true);

						}

					});

					if ( item < 0) {
						this.activate( items[0]);

					}
					else {
						item ++;
						if ( item > items.length -1)
							this.activate( items[0]);
						else
							this.activate( items[item]);

					}

					//~ console.log( item);

				}

			},

			selectitem : function( event) {
				keyMove.activate( this);

				let item = $(this).data('item');
				_me.val( !!item.value ? item.value : ( !!item.label ? item.label : item));
				if ( !!event.stopPropagation) {
					/*
					* this stops the click going through to an underlying element
					*/
					event.stopPropagation(); event.preventDefault();

				}

				keyMove.clear();

				if ( 'function' == typeof options.select)
					options.select( event, {item:item});

			},

			select : function( event) {
				var items = this.items();
				if ( items.length > 0) {
					var item = -1;
					$.each( items, function( i, el) {
						if ( $(el).hasClass('active')) {
							item = i;
							return ( false);

						}

					});

					if ( item > -1) {
						this.selectitem.call( items[item], event);

					}
					else if ( !!event && ( 13 == event.keyCode || 9 == event.keyCode)) {
						if ( options.activateOnEnter || options.autoFocus) {
							this.selectitem.call( items[0], event);

						}

					}

				}

			},

			clear : function() {
				list.html('');
				this.current = false;

			},

			_initialized : false,

			init : function() {
				if ( this._initialized) return;
				/*
				* initialise placement of the list item
				*
				* this is sized and placed the first time it is used
				* the element should be positioned and sized correctly
				* by now.
				*/

				this._initialized = true;

				/*-- --[ position exactly where ? ]-- --*/
				if ( 'static' == options.appendTo.css('position')) {
					options.appendTo.css('position', 'relative');

				}

				let _mePos = _me.offset();
				let parentPos = options.appendTo.offset();
				let childOffset = {
					position : 'absolute',
					top: _mePos.top - parentPos.top + _me.outerHeight(),
					left: _mePos.left - parentPos.left
				};
				childOffset.width = Math.max( _me.outerWidth(), options.minWidth);
				options.wrapper.css( childOffset);
				/*-- --[ position exactly where ? ]-- --*/

			}

		};

		let lastVal = '';
		let iterant = 0;
		let blurTimeOut = false;

		_me.on( 'focus.autofill', function() {
			if ( blurTimeOut) {
				window.clearTimeout( blurTimeOut);
				blurTimeOut = false;

			}

		})
		.on( 'blur.autofill', function() {

			if ( blurTimeOut) {
				window.clearTimeout( blurTimeOut);
				blurTimeOut = false;

			}

			blurTimeOut = window.setTimeout( function() {
				//~ console.log( 'setTimeout :: clear');
				keyMove.clear();
				blurTimeOut = false;

			}, 900);

		})
		.on( 'keydown.autofill', function ( e) {
			//~ console.log( 'keydown.autofill', e.keyCode);
			if ( e.keyCode == 9 && options.autoFocus) {
				keyMove.select( e);
				return;

			}

		})
		.on( 'keypress.autofill', function ( e) {
			/*
			* allowing enter through will probably submit the form,
			* enter just settles here
			*/
			//~ console.log( 'keypress.autofill', e.keyCode);
			return ( e.keyCode || e.which || e.charCode || 0) !== 13;

		})
		.on( 'keyup.autofill', function( e) {
			if ( e.shiftKey)
				return;

			if ( !_brayworth_.browser.isMobileDevice) {
				// console.log( 'keyup.autofill', e.keyCode);
				if ( e.keyCode == 13) {
					keyMove.select( e);
					return;

				}
				else if ( e.keyCode == 38 ) {
					keyMove.up();
					return;

				}
				else if ( e.keyCode == 40 ) {
					keyMove.down();
					return;

				}

			}

			if ( _me.val().length < options.minLength || _me.val() == lastVal)
				return;

			lastVal = _me.val();

			let _data = {
				term : lastVal,
				iterant : ++iterant,

			};

			//~ console.log( typeof options.source);

			if ( /^(array|object)$/.test( typeof options.source)) {
				keyMove.clear();
				keyMove.init();

				//~ console.table( options.source);

				let rex = new RegExp( lastVal);

				$.each( options.source, function( i, el) {

					if ( !!el.label) {
						if ( rex.test( el.label)) {
							$('<li class="list-group-item p-1" tabindex="-1"></li>')
								.append( $('<div class="text-truncate"></div>').html( el.label))
								.data( 'item', el)
								.on( 'click', function( e) { keyMove.selectitem.call( this, e); })
								.on( 'mouseover', function() { keyMove.activate( this); })
								.appendTo( list);

						}

					}
					else {
						if ( rex.test( el)) {
							$('<li class="list-group-item p-1" tabindex="-1"></li>')
								.append( $('<div class="text-truncate"></div>')
								.html( el)).data( 'item', { label:el, value:el })
								.on( 'click', function( e) { keyMove.selectitem.call( this, e); })
								.on( 'mouseover', function() { keyMove.activate( this); })
								.appendTo( list);

						}

					}

				});

			}
			else {
				setTimeout(() => {
					if ( _data.iterant != iterant) {
						return;

					}

					let render = function( el) {
						//~ console.log( _data.term, el);

						let _pad = $('<div class="text-truncate" tabindex="-1"></div>')	;
						_pad.html( !!el.label ? el.label : ( !!el.value ? el.value : el));
						//~ console.log( _pad.html());

						let _li = $('<li class="list-group-item p-1" tabindex="-1"></li>').append( _pad);

						let touchStartTimeout = false;

						_li.data( 'item', el)
						.css('border','1px solid dashed')
						.on( 'mousedown', function( event ) {
							// Prevent moving focus out of the text field
							event.preventDefault();

						})
						.on( 'click', function( e) {
							keyMove.selectitem.call( this, e);

						})
						.on( 'touchstart', function( e) {
							let _me = this;
							keyMove.activate( _me);

							touchStartTimeout = window.setTimeout( function() {
								keyMove.deactivate( _me);

							}, 300);

						})
						.on( 'touchend', function( e) {

							if ( $(this).hasClass('active')) {
								if (touchStartTimeout) {
									window.clearTimeout( touchStartTimeout);

								}

								keyMove.selectitem.call( this, e);

							}

						})
						.on( 'mouseover', function() {
							keyMove.activate( this);

						});

						return ( _li);

					};

					options.source( _data, ( data) => {
						keyMove.clear();
						keyMove.init();
						$.each( data, ( i, el) => { list.append( render( el)); });

					});

				}, options.timeout);

			}

		});

		return _me; // chain

	}

})(jQuery);
