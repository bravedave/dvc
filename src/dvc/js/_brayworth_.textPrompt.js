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
_brayworth_.textPrompt = function( params) {
	return new Promise( function( resolve, reject) {
		let options = {
			title : 'Enter Text',
			text : '',
			verbatim : ''

		};

		$.extend( options, params);

		let input = $('<input type="text" class="form-control" autofocus />')
			.val( options.text)
			.attr( 'placeholder', options.title);

		let div = $('<div></div>').attr( 'title', options.title);
		input.appendTo( div);

		if ( options.verbatim != '')
			div.append( $('<div class="text-muted font-italic"></div>').html( options.verbatim));

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