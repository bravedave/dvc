/*
	David Bray
	D'Arcy Estate Agents & BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Attribution-NonCommercial-NoDerivatives 4.0 International Public License.
		https://creativecommons.org/licenses/by-nc-nd/4.0/

	*/
/*jshint esversion: 6 */
_brayworth_.toaster = function() {};

$(document).ready( function() {
	_brayworth_.toaster = (function() {
		let adjustTop = function() {};
		let wrap = $('<div style="position: absolute; top: 50px; right: 20px; width: 300px"></div>');
		let nav = $('[role="growler"]');
		let mode = 'append';

		if ( nav.length > 0) {
			mode = 'prepend';
			wrap = $('<div style="position: absolute; top: -4rem; left: 5px; width: 290px"></div>');
		}
		else {
			nav = $('body > nav.sticky-top');

		}

		if ( nav.length > 0) {
			wrap.appendTo( nav[0]);

		}
		else {
			wrap.appendTo( 'body');
			adjustTop = function() {
				//~ console.log({ 'top' : ($(window).scrollTop() + 50) + 'px'});
				wrap.css({ 'top' : ($(window).scrollTop() + 50) + 'px'});

			};

		}

		return function( params) {
			let options = {
				title : 'Info',
				text : '...',
				delay : 2000,
				growlClass : 'success'

			};

			if ( 'string' == typeof params) {
				options.text = params;

			}
			else {
				options = _brayworth_.extend( options, params);

				if ( options.title == 'Info' || options.text == '...') {
					/*
					* a little repetitive - it's an ajax response
					*
					* the basic ajax response is:
					* { response : 'ack', description : 'go you good thing' }
					*
					* with an optional timeout set to 0 it will become a bootstrap 4valert:
					* { response : 'ack', description : 'go you good thing', timeout : 0 }
					*/
					if (!!params.response) { options.growlClass = ( /(ack|ok)/i.test( params.response) ? 'success' : 'error' ); }

					if ( !!params.description) { options.text = params.description; }

					if ( options.growlClass == 'error' ) options.delay = 6000;

				}

			}

			return new Promise( function( resolve, reject) {

				let timestamp = _brayworth_.moment();

				let toast = $('<div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-autohide="true"></div>').attr( 'data-delay', options.delay);
				let header = $('<div class="toast-header"></div>').appendTo(toast);

				if ( options.growlClass == 'error' ) {
					header.append( '<svg class="bd-placeholder-img rounded mr-2" width="20" height="20" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice" focusable="false" role="img"><rect fill="#dc3545" width="100%" height="100%"></rect></svg>');
				} else {
					header.append( '<svg class="bd-placeholder-img rounded mr-2" width="20" height="20" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice" focusable="false" role="img"><rect fill="#007aff" width="100%" height="100%"></rect></svg>');
				}

				$('<strong class="mr-auto" />').html( options.title).appendTo( header);
				let timer = $('<small class="text-muted ml-2">just now</small>').appendTo( header);
				$('<button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close"><span aria-hidden="true">&times;</span></button>').appendTo( header);

				$('<div class="toast-body"></div>').html( options.text).appendTo(toast);

				adjustTop();
				toast.on('hidden.bs.toast', function( e) {
					$(this).remove();
					resolve(e);

				})

				if ('prepend' == mode) {
					toast.prependTo( wrap).toast('show');
				} else {
					toast.appendTo( wrap).toast('show');
				}

				let utime = function( toast, timer, timestamp, utime) {
					let d = moment.duration( _brayworth_.moment().diff( timestamp));
					timer.html( parseInt( d.as('seconds')) + ' second(s) ago');

					if ( toast.hasClass('show')) {
						//~ console.log( 'set update', d.as('seconds'));
						setTimeout( utime, 1000, toast, timer, timestamp, utime);

					}

				};

				setTimeout( utime, 1000, toast, timer, timestamp, utime);

			});

		};

	})();

});
