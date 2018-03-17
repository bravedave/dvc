<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/
NameSpace dvc;

abstract class errsys {
	protected static $_shutup = FALSE;
	protected static $_currentUser = FALSE;

	public static function shutup( $state = NULL ) {
		$ret = self::$_shutup;

		if ( !( is_null( $state )))
			self::$_shutup = $state;

		return ( $ret );

	}

	public static function currentUser( $name = NULL ) {
		$ret = self::$_currentUser;

		if ( !( is_null( $name )))
			self::$_currentUser = $name;

		return ( $ret );

	}

	public static function initiate( $log = false ) {
		set_error_handler(function( $errno, $errstr, $errfile, $errline, $errcontext) {
			errsys::err_handler( $errno, $errstr, $errfile, $errline, $errcontext);
		});

		set_exception_handler(function( $e ) {
			errsys::exc_handler( $e );
		});

		if ( $log !== false ) {
			if ( ! ini_get('log_errors') )
				ini_set('log_errors', true);
			if ( ! ini_get('error_log') )
				ini_set('error_log', $log);

		}

	}

	static public function err_handler( $errno, $errstr, $errfile, $errline, $errcontext) {
		if ( self::$_shutup )
			return;

		$l = error_reporting();
		if ( $l & $errno ) {
			$exit = false;

			switch ( $errno ) {
				case E_USER_ERROR:
					$type = 'Fatal Error';
					$exit = true;
					break;
				case E_USER_WARNING:
				case E_WARNING:
					$type = 'Warning';
					break;
				case E_USER_NOTICE:
				case E_NOTICE:
				case @E_STRICT:
					$type = 'Notice';
					break;
				case @E_RECOVERABLE_ERROR:
					$type = 'Catchable';
					break;
				default:
					$type = 'Unknown Error';
					$exit = true;
					break;
			}

			/*
			 * error is logged in the exception
			 */
			error_log( '---[probable duplicate    : error is logged in the exception]---');
			error_log( sprintf( '%s: %s %s %s %s', $type, $errstr, $errno, $errfile, $errline));
			error_log( '---[end probable duplicate: error is logged in the exception]---');
			$exception = new \Exception( sprintf( '%s: %s %s %s %s %s', $type, $errstr, $errno, PHP_EOL, $errfile, $errline));

			if ( $exit ) {
				self::exc_handler( $exception);
				exit();

			}
			else
				throw $exception;

		}
		return FALSE;

	}

	static public function exc_handler( $e ) {
		if ( self::$_shutup )
			return;

		if ( method_exists($e, 'format' ))
			$message = $e->format();

		else
			$message = sprintf( "%s(%s)\n", $e->getMessage(), $e->getCode());

		printf( "ERROR<hr /><pre>%s</pre><hr /><a href='%s'>return to home page</a>", $message, \url::$URL );

		error_log( $message);
		self::email_support( $e, $message );

	}

	static public function email_support( $e, $exposed = '' ) {
		if ( method_exists($e, 'format' )) {
			$mailMessage = $e->format();

		}
		else {
			$mailMessage = sprintf( "%s(%s)\n", $e->getMessage(), $e->getCode()) .
				sprintf( "%s(%s)\n", $e->getFile(), $e->getLine() ) .
				sprintf( "%s\n", $e->getTraceAsString()) .
				sprintf( "--------------------------------------------\nExposed:\n%s\n", $exposed );
			if( isset($_SERVER['HTTP_REFERER']))
				$mailMessage .= sprintf( "Referer: %s\n", $_SERVER['HTTP_REFERER']);

			if ( self::$_currentUser)
				$mailMessage .= sprintf( "Current User:%s\n", self::$_currentUser );

		}


		if ( \config::$EMAIL_ERRORS_TO_SUPPORT ) {
			$header = array(
				sprintf( 'From: %s <%s>', \config::$WEBNAME, \config::$WEBEMAIL ),
				sprintf( 'Reply-To: %s <%s>', \config::$WEBNAME, \config::$SUPPORT_EMAIL ),
				sprintf( 'Return-Path: %s <%s>', \config::$WEBNAME, \config::$SUPPORT_EMAIL ),
				'Content-Type: text/plain',
				sprintf( 'Date: %s', date(DATE_RFC2822)) );

			// These two to help avoid spam
			$host = '';
			if ( isset( $_SERVER['SERVER_NAME'] ))
				$host = $_SERVER['SERVER_NAME'];
			else
				$host = getenv('HOSTNAME');

			$header[] = sprintf( 'Message-ID: <%s>', date('YmdHis') . 'TheSystem@' . $host);
			$header[] = sprintf( 'X-Mailer: PHP v%s', phpversion());

			$headers = implode( "\r\n", $header );
			// $scriptname = strtolower( $_SERVER[ "SCRIPT_NAME" ]);

			try {
				$mail = \sys::mailer();
				$mail->IsHTML(false);
				$mail->CharSet = 'UTF-8';
				$mail->Encoding = 'base64';

				$mail->Subject  = \config::$WEBNAME . " PHP Error";
				$mail->AddAddress( \config::$SUPPORT_EMAIL, \config::$SUPPORT_NAME );

				$mail->Body = $mailMessage;
				if ( $mail->send()) {
					\sys::logger( 'error - send email');

				}
				else {
					\sys::logger( 'error - send email failed - fallback to mail ' . $mail->ErrorInfo);



					mail( \config::$SUPPORT_EMAIL, \config::$WEBNAME . " PHP Error", $mailMessage, $headers, "-f" . \config::$SUPPORT_EMAIL );

				}

			}
			catch ( \Exception $e) {
				mail( \config::$SUPPORT_EMAIL, \config::$WEBNAME . " PHP Error", $mailMessage, $headers, "-f" . \config::$SUPPORT_EMAIL );

			}
			catch( \Exception $e) {
				print '<h1>Could not send error report</h1>';
				print $mailMessage;

			}

		}
		else {
			error_log( $mailMessage);

		}

	}

}
