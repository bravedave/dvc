/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/


	tests :
	 	_brayworth_.growl( 'howdy');
		_brayworth_.growl({
			growlClass : 'error',
			text : 'not good',
		});
		_brayworth_.growl({
			response : 'nak',
			description : 'not good',
		});

*/

(function() {

	_brayworth_.growlSuccess = function(params) {
		var options = { growlClass : 'success' }

		if ( /object/.test( typeof params))
			$.extend( options, params);
		else if ( /string/i.test( typeof params ))
			options.text = params;

		return ( _brayworth_.growl.call( this, options));

	}

	_brayworth_.growlError = function(params) {
		var options = { growlClass : 'error', timeout : 5000 }

		if ( /object/.test( typeof params))
			$.extend( options, params);
		else if ( /string/i.test( typeof params ))
			options.text = params;

		return ( _brayworth_.growl.call( this, options));

	}

	_brayworth_.growlAjax = function( j) {
		/*
			standard ajax response is {
				response : 'ack or nak',
				description : 'blah blah ..'
			}
		 */

		var options = { growlClass : 'error', text : 'no description' }
		if ( !!j.response && j.response == 'ack')
			options.growlClass = 'success';

		if ( !!j.description)
			options.text = j.description;

		if ( options.growlClass == 'error')
			options.timeout = 0;

		if ( !!j.timeout)
			options.timeout = j.timeout;

		console.log( options);

		return ( _brayworth_.growl.call( this, options));

	}

	var growlers = [];
	_brayworth_.growl = function( params) {

		var host = ( this == _brayworth_ ? $('body') : this)
		if ( 'string' == typeof this) {
			host = $(host);

		}
		else if ( this instanceof String) {
			host = $(host.valueOf());

		}
		else if ( 'object' == typeof this  && !!this.xhr) {
			host = $('body');

		}
		else if ( !( this instanceof jQuery)) {
			host = $(host);

		}

		return new Promise( function( resolve, reject) {

			//~ console.log( typeof this, this instanceof String);
			//~ console.log( host);

			var options = {
				top : 60,
				right : 20,
				text : '',
				title : '',
				timeout : 2000,
				growlClass : 'information',

			}

			if ( 'object' == typeof params) {
				$.extend( options, params);

			}
			else if ( 'string' == typeof params ) {
				options.text = params;

			}

			if ( options.title == '' || options.text == '') {
				/*
				* a little repetitive - it's an ajax response
				*
				* the basic ajax response is:
				* { response : 'ack', description : 'go you good thing' }
				*
				* with an optional timeout set to 0 it will become a bootstrap 4valert:
				* { response : 'ack', description : 'go you good thing', timeout : 0 }
				*/
				if ( !!params.response)
					options.growlClass = ( params.response == 'ack' ? 'success' : 'error' );

				if ( !!params.description)
					options.text = params.description;

			}

			if ( !!params.timeout) {
				options.timeout = params.timeout;

			}
			else if ( options.growlClass == 'error') {
				options.timeout = 0;

			}

			if ( options.title == '' && options.text == '')
				return;	// abandon ship

			if ( 0 == options.timeout) {
				/*
				* with an optional timeout set to 0 it will become a bootstrap 4valert:
				* { response : 'ack', description : 'go you good thing', timeout : 0 }
				*/
				let growler = $('<div class="alert alert-warning alert-dismissible fade show m-1" role="alert" />');
				if ( options.growlClass == 'error') {
					growler.removeClass('alert-warning').addClass('alert-danger');

				}

				if ( options.title != '') {
					let title = $('<h3 />').html( options.title).appendTo( growler);

				}

				$('<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>').appendTo(growler);

				if ( options.text != '') {
					let content = $('<div />').html(options.text).appendTo( growler);

				}

				growler.prependTo('body');

			}
			else {
				let growler = $('<div class="growler" />');

				/*
				* you have to find a place in the growlers for this one
				*/
				let growlerIndex = -1
				$.each( growlers, function( i, e) {
					if ( !e) {
						growlerIndex = i;
						growlers[growlerIndex] = growler;
						//~ console.log( 'growlerIndex - recycle', growlerIndex);
						return ( false);

					}

				});

				if ( growlerIndex < 0) {
					// grow the index
					growlerIndex = growlers.length;
					growlers[growlerIndex] = growler;
					//~ console.log( 'growlerIndex - new', growlerIndex);

				}

				if ( host[0].tagName == 'BODY' || host.css('position') != 'static') {
					options.top *= growlerIndex;	// growler is offset down screen to avoid stacking
				}
				else {
					try {
						var offset = host.offset();
						options.top = offset.top - 20;
						options.right = Math.min( $(window).width(), offset.left + host.width() + 20);

						//~ console.log( options.top, options.right);

					}
					catch (e) {
						console.warn( host, e);

					}

				}

				options.top = Math.max( options.top, $(window).scrollTop());

				var title = $('<h3 />');
				var content = $('<div />');

				options.title != '' ?
					title.html(options.title).appendTo( growler) :
					content.css('padding-top','5px');

				if ( options.text != '')
					content.html(options.text).appendTo( growler);

				growler
				.css({ 'position' : 'absolute', 'top' : options.top, 'right' : options.right })
				.addClass(options.growlClass)
				.appendTo( host);

				setTimeout( function() {
					growlers[growlerIndex] = false;
					growler.remove();
					resolve();
				}, options.timeout)

			}

		});

	}

})();