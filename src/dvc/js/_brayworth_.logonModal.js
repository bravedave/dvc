/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 *	test:
 *		_brayworth_.logonModal();
 *
 * */
/*jshint esversion: 6 */
( _ => {
	_.logonModal = () => {
		let modal = false;
		let flds = {
			user: $('<input type="text" class="form-control" placeholder="username or email" autocomplete="username" />'),
			pass: $('<input type="password" class="form-control" placeholder="password" autocomplete="current-password" />'),

		};

		let form = $('<form></form>');
		$('<div class="form-group"></div>').append(flds.user).appendTo(form);
		$('<div class="form-group"></div>').append(flds.pass).appendTo(form);

		let submitter = () => {
			let u = flds.user.val();
			let p = flds.pass.val();

			if (u.trim() == '') {
				$('.text-danger', form).remove();
				$('<div class="text-danger">user cannot be empty</div>').insertAfter(flds.user);
				flds.user.focus();
				return;

			}

			if (p.trim() == '') {
				$('.text-danger,.text-warning', form).remove();
				$('<div class="text-danger">empty password</div>').insertAfter(flds.pass);
				flds.pass.focus();
				return;

			}

			modal.modal('close');

			_.post({
				url : _.url(),
				data: {
					action: '-system-logon-',
					u: u,
					p: p,

				}

			})
			.then( d => {
				$('body').growl(d);
				if ('ack' == d.response) {
					window.location.reload(true);

				}
				else {
					setTimeout(_.logonModal, 2000);

				}

			});

		}

		form.on('submit', function () {
			submitter();
			return false;

		})
		.append('<input type="submit" style="display: none;" />');

		let buttons = {};
		if (_.logon_retrieve_password) {
			buttons['Reset Password'] = () => {
				let u = flds.user.val();

				if (u.trim() == '') {
					$('.text-danger,.text-warning', form).remove();
					$('<div class="text-danger">user cannot be empty</div>').insertAfter(flds.user);
					flds.user.focus();
					return;

				}

				console.log(_.url());

				_.post({
					url: _.url(),
					data: {
						action: '-send-password-',
						u: u,

					}

				})
				.then( d => {
					console.log( d);
					_.growl(d);
					if ('ack' == d.response) {
						_.modal({
							width: 300,
							title: d.description,
							text: d.message,
							buttons: {
								OK: function (e) {
									this.modal('close');
									_.logonModal();

								}

							}

						});

					}
					else {
						$('.text-danger,.text-warning', form).remove();
						$('<div class="text-warning"></div>').appendTo(form);

					}

				});

			};

		}

		buttons.logon = submitter;

		modal = _.modal({
			className: 'modal-sm',
			title: 'logon',
			text: form,
			buttons: buttons,
			onOpen: e => { flds.user.focus(); }

		});

	};

}) (_brayworth_);

