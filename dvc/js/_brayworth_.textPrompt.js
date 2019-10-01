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
_brayworth_.textPrompt = function( params, callback) {
	return new Promise( function( resolve, reject) {
		let options = {
			title : 'Enter Text',
			text : '',
			verbatim : ''

		};

		$.extend( options, params);

		let rand = parseInt( Math.random() * 10000);
		let div = $('<div />').attr( 'title', options.title);
		let input = $('<input type="text" class="form-control" autofocus />')
			.val( options.text)
			.attr( 'placeholder', options.title)
			.attr( 'id', '_input' + rand);

		let lbl = $( '<label />').html( options.title).attr( 'for', '_input' + rand);

		$('<div class="form-label-group" />').append( input).append( lbl).appendTo( div);

		if ( options.verbatim != '')
			$('<div class="text-muted font-italic" />').html( options.verbatim).appendTo( div);

		//~ console.log( 'click');

		_brayworth_.modal.call( div, {
			className : '',
			buttons : {
				cancel : function(e) {
					this.modal( 'close');

				},
				Ok : function(e) {
					options.text = input.val();
					if ( '' == options.text) {
						_cms_.growl('text is missing');
						input.focus();

					}
					else {

						this.modal( 'close');

						if ( /function/i.test( typeof callback))
							callback.call( options);

						resolve( options.text);

					}

				}

			},
			close : function() {
				$( this ).remove();

			}

		});

	});

}
;