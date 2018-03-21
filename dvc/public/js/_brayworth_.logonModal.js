/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	test:
		_brayworth_.logonModal();

	*/
_brayworth_.logonModal = function() {
	var flds = {
		user : $('<input type="text" class="form-control" placeholder="username or email" autocomplete="username" />'),
		pass : $('<input type="password" class="form-control" placeholder="password" autocomplete="current-password" />'),
	}

	var dlg = $('<div class="container" />');
	var form = $('<form class="form" />').appendTo( dlg);
		$('<div class="row py-1" />').append( $( '<div class="col" />').append( flds.user)).appendTo( form);
		$('<div class="row py-1" />').append( $( '<div class="col" />').append( flds.pass)).appendTo( form);

	function submitter() {
		var u = flds.user.val();
		var p = flds.pass.val();

		if ( u.trim() == '') {
			$('body').growlError( 'empty user');
			flds.user.focus();
			return;

		}

		if ( p.trim() == '') {
			$('body').growlError( 'empty pass');
			flds.pass.focus();
			return;

		}

		modal.modal('close');

		$.ajax({
			type : 'post',
			url : _brayworth_.url(),
			data : {
				action : '-system-logon-',
				u : u,
				p : p,

			}

		})
		.done( function( d) {
			$('body').growlAjax( d);
			if ( 'ack' == d.response) {
				window.location.reload();

			}
			else {
				setTimeout( _brayworth_.logonModal, 2000);

			}

		});

	}

	function retrievePassword() {
		var u = flds.user.val();

		if ( u.trim() == '') {
			$('body').growlError( 'empty user');
			flds.user.focus();
			return;

		}

		$.ajax({
			type : 'post',
			url : _brayworth_.urlwrite(),
			data : {
				action : '-send-password-',
				u : u,

			}

		})
		.done( function( d) {
			$('body').growlAjax( d);
			if ( !!d.response && d.response == 'ack') {
				_brayworth_.modal({
					width : 300,
					title : d.description,
					text : d.message,
					buttons : {
						OK : function(e) {
							$(this).modal( 'close');
							flds.user.focus();

						}

					}

				});

			}

		});

	}

	form.on( 'submit', function() {
		submitter();
		return false;

	})
	.append( '<input type="submit" style="display: none;" />');

	var buttons = {};
	if ( _brayworth_.logon_retrieve_password)
		buttons['Reset Password'] = retrievePassword;

	buttons.logon = submitter;

	var modal = _brayworth_.modal({
		width : 300,
		title : 'logon',
		text : dlg,
		buttons : buttons

	});

};
