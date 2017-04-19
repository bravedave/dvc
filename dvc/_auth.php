<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	DO NOT change this file
	It is not called directly, the values here are fallback values

	Modify auth.php
	*/
NameSpace dvc;

abstract class _auth {
	static function GoogleAuthEnabled() {
		if ( is_null( config::$oauth2_client_id) || is_null( config::$oauth2_secret) || is_null( config::$oauth2_redirect ))
			return ( FALSE);

		return ( TRUE);

	}

	static function FacebookAuthEnabled() {
		if ( is_null( config::$facebook_oauth2_client_id) || is_null( config::$facebook_oauth2_secret) || is_null( config::$facebook_oauth2_redirect))
			return ( FALSE);

		return ( TRUE);

	}

	static function dialog() {
		$top = new html\div();
			$top->attributes( array(
				'class' => 'modal fade',
				'id' => 'dlgLogon',
				'tabindex' => '-1',
				'role' => 'dialog',
				'aria-labelledby' => 'myModalLabel',
				'aria-hidden' => 'true' ));

			$div = $top->append( 'div', NULL, array(
				'class' => 'modal-dialog modal-sm',
				'id' => 'dlgLogonDialog' ));

				$content = $div->append( 'div', NULL, array(
					'class' => 'modal-content' ));

					$header = $content->append( 'div', NULL, array(
						'class' => 'modal-header' ));

						$button = $header->append( 'button', NULL, array(
							'type' => 'button',
							'class' => 'close',
							'data-dismiss' => 'modal',
							'aria-label' => 'Close' ));

							$button->append( 'span', '&times;', array( 'aria-hidden' => 'true' ));

						$header->append( 'h4', 'Sign in to you account using', array(
							'class' => 'modal-title',
							'id' => 'dlgLogonTitle' ));

					$body = $content->append( 'div', NULL, array(
						'class' => 'modal-body',
						'id' => 'dlgLogonBody' ));

						$a = new html\a( \url::$URL . ( currentUser::valid() ? 'auth/logout' : 'auth/request' ),'');
						if ( currentUser::valid()) {
							$a->appendChild( new html\img( \url::$URL . 'images/logout-left9x54.png', 'logout'));
							$img = new html\img( currentUser::avatar(), 'avatar');
								$img->attributes( array( 'title' => currentUser::DisplayName()));
							$a->appendChild( $img);
							$a->appendChild( new html\img( \url::$URL . 'images/logout-63x54.png', 'logout'));

						}
						else {
							$a->appendChild( new html\img( \url::$URL . 'images/g-signin-266x54.png', 'logon with google' ));

						}

						$body->appendChild( $a);

						$a = new html\a( \url::$URL . ( currentUser::valid() ? 'fbauth/logout' : 'fbauth/request' ),'');
						if ( currentUser::valid()) {
							$a->appendChild( new html\img( \url::$URL . 'images/logout-left9x54.png', 'logout'));
							$img = new html\img( currentUser::avatar(), 'avatar');
								$img->attributes( array( 'title' => currentUser::DisplayName()));
							$a->appendChild( $img);
							$a->appendChild( new html\img( \url::$URL . 'images/logout-63x54.png', 'logout'));

						}
						else {
							$a->appendChild( new html\img( \url::$URL . 'images/fb-signin-266x54.png', 'logon with facebook' ));

						}

						$body->appendChild( $a);

						$a = new html\a( \url::$URL . ( currentUser::valid() ? 'odauth/logout' : 'odauth/request' ),'');
						if ( currentUser::valid()) {
							$a->appendChild( new html\img( \url::$URL . 'images/logout-left9x54.png', 'logout'));
							$img = new html\img( currentUser::avatar(), 'avatar');
								$img->attributes( array( 'title' => currentUser::DisplayName()));
							$a->appendChild( $img);
							$a->appendChild( new html\img( \url::$URL . 'images/logout-63x54.png', 'logout'));

						}
						else {
							$a->appendChild( new html\img( \url::$URL . 'images/ms-signin-266x54.png', 'logon with microsoft' ));

						}

						$body->appendChild( $a);

	}

	static function button() {
		if ( auth::GoogleAuthEnabled()) {
			if ( currentUser::valid()) {
				return ( sprintf( '<a href="%s"><img alt="logout" src="%s" /><img alt="avatar" class="user-avatar" title="%s" src="%s" /><img alt="logout" src="%s" /></a>',
					url::tostring( 'auth/logout'),
					url::tostring( 'images/logout-left9x54.png'),
					currentUser::user()->name,
					currentUser::avatar(),
					url::tostring( 'images/logout-63x54.png')
					));

			}
			else {
				return ( sprintf( '<a href="#dlgLogon" data-toggle="modal"><img alt="logon with google" src="%s" /></a>',
					url::tostring( 'images/gfb-signin-246x54.png' )));

			}

		}

		return ( '');

	}

}
