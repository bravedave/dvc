/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */
/*jshint esversion: 6 */
( _ => {
	_.textPrompt = function( params) {
		return new Promise( function( resolve, reject) {
			let options = _.extend({
				title : 'Enter Text',
				text : '',
				verbatim : ''
			}, params);

			let input = $('<input type="text" name="text" class="form-control" required />')
				.val( options.text)
				.attr( 'placeholder', options.title);

			let modal = $('<div class="modal fade" tabindex="-1" role="dialog"></div>');
			let content = $('<div class="modal-content"></div>');

			$('<div class="modal-dialog modal-dialog-centered" role="document"></div>')
			.appendTo(modal).append( content);

			let header = $('<div class="modal-header py-2"></div>').appendTo( content);

			$('<h5 class="modal-title"></h5>').html( options.title).appendTo( header);

			header.append( '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>');

			let body = $('<div class="modal-body"></div>').append( input).appendTo( content);

			$('<div class="modal-footer py-0"><button type="submit" class="btn btn-outline-primary">OK</button></div>').appendTo( content);

			if ( options.verbatim != '') {
				body.append( $('<div class="form-text text-muted font-italic"></div>').html( options.verbatim));

			}

			let form = $('<form></form>').append( modal).appendTo( 'body');

			form
			.on('submit', function (e) {
				let _form = $(this);
				let _data = _form.serializeFormJSON();

				options.text = _data.text;
				resolve(options.text);

				modal.modal( 'hide');
				return false;

			});

			modal.on('shown.bs.modal', e => { input.focus(); input.select(); });
			modal.on( 'hidden.bs.modal', e => { form.remove(); });
			modal.modal( 'show');

		});

	};

}) (_brayworth_);
