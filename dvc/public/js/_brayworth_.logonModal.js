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
		user : $('<input type="text" class="form-control" placeholder="username" />'),
		pass : $('<input type="password" class="form-control" placeholder="password" />'),
	}

	var dlg = $('<div class="container" />');
	var form = $('<form class="form" />').appendTo( dlg);
		$('<div class="row" />').append( $( '<div class="col" />').append( flds.user)).appendTo( form);
		$('<div class="row" />').append( $( '<div class="col" />').append( flds.pass)).appendTo( form);

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
			url : _brayworth_.urlwrite(),
			data : {
				action : '-system-logon-',
				u : u,
				p : p,

			}

		})
		.done( function( d) {
			$('body').growlAjax( d);
			if ( !!d.response && d.response == 'ack')
				window.location.reload();

			else
				setTimeout( _brayworth_.logonModal, 2000);


		});

	}

	form.on( 'submit', function() {
		submitter();
		return false;

	})
	.append( '<input type="submit" style="display: none;" />');

	var modal = _brayworth_.modal({
		width : 300,
		title : 'logon',
		text : dlg,
		buttons : {
			logon : submitter

		}

	});

};
