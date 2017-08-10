<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/
NameSpace dvc;

abstract class Response {

	static function redirect( $url = NULL, $message = "", $auto = TRUE ) {

		if ( is_null( $url )) {
			$url = \url::$URL;
			//~ sys::logger( 'Set URL:' . $url );

		}
		elseif ( ! ( preg_match( '@^(http|//)@i', (string)$url ))) {
			$url = \url::$URL . $url;
			//~ sys::logger( 'Fixed URL:' . $url );

		}

		//~ sys::logger( 'redirect at:' );
		//~ foreach ( debug_backtrace() as $e )
			//~ sys::logger( sprintf( '%s(%s)', $e['file'], $e['line'] ));

		if ( $message == "" ) {
			header( "location: $url\n" );
			//~ sys::logger( 'Going to URL:' . $url );
			exit;

		}

		\Page::$MainContextMenu = FALSE;

		$p = new \Page();
			$p->title = $message;
			$p->footer = FALSE;
			$p->additionalScripts = array();
		if ( userAgent::isMobileDevice())
			$p->meta[] = '<meta name="viewport" content="initial-scale=0.8" />';

		if ( $auto ) {
			$p->header( FALSE );
			print <<<OUTPUT
	<meta http-equiv="refresh" content="1; url=$url" />
</head>
<body>

OUTPUT;

		}
		else {
			$p->header();

		}

		printf( '<div style="height: 50px;">&nbsp;</div><div style="margin: auto; padding: 20px; border: 1px solid silver; max-width: 600px;">

		<p style="font-size: 14pt; margin: 30px;">%s</p>

		<div style="text-align: right; padding-right: 20x;">
			<a style="text-decoration: none; font-style: italic;" href="%s">%s .... .</a>

		</div>

	</div>', $message,  $url, ( $auto ? 'redirecting' : 'continue' ));

		exit;	// don't run anything else

	}

	static function html_docType() {
		$docType = ( userAgent::isIE() ? 'html4' : 'html5' );

		if ( $docType == 'html5' ) {
			return ( "<!DOCTYPE html>\n<html lang=\"en\">" );

		} else {
			return ( "<!DOCTYPE html
		PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"
		\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">" );

		}

	}

	static function json_headers( $modifyTime = 0) {
		self::_common_headers( $modifyTime);
		header('Content-type: application/json');

	}

	static function excel_headers( $filename = "download.xml" ) {
		self::_common_headers();
		header("Content-Description: File Transfer");
		header("Content-Disposition: attachment; filename=$filename");
		header("Content-type: application/vnd.ms-excel");
	}

	static function xml_headers( $modifyTime = 0) {
		Response::_common_headers( $modifyTime);
		header('Content-type: text/xml');

	}

	static function css_headers( $modifyTime = 0, $expires = NULL) {

		if ( is_null( $expires)) $expires = \config::$CSS_EXPIRE_TIME;

		self::_common_headers( $modifyTime, $expires);
		header('Content-type: text/css');

	}

	static function html_headers( $charset = FALSE) {
		if ( !$charset)
			$charset = 'UTF-8';

		Response::_common_headers();

		if ( \config::$CONTENT_SECURITY_ENABLED)
			header( "Content-Security-Policy: frame-ancestors 'self'");

		header( sprintf( "Content-type: text/html; charset=%s", $charset));

	}

	static function text_headers( $modifyTime = 0, $expires = 0) {
		self::_common_headers( $modifyTime, $expires);
		header("Content-type: text/plain");

	}

	static function javascript_headers( $modifyTime = 0, $expires = 0) {
		self::_common_headers( $modifyTime, $expires);
		header('Content-type: text/javascript');

	}

	static function icon_headers( $modifyTime = 0, $expires = NULL) {

		if ( is_null( $expires)) $expires = \config::$IMG_EXPIRE_TIME;

		self::_common_headers( $modifyTime, $expires);
		header('image/x-icon');

	}

	static function headers( $mimetype, $modifyTime = 0, $expires = 0) {
		self::_common_headers( $modifyTime, $expires);
		header( sprintf( 'Content-type: %s', $mimetype));

	}

	static function csv_headers( $filename = "download.csv", $modifyTime = 0, $expires = 0) {
		self::_common_headers( $modifyTime, $expires);
		header("Content-Description: File Transfer");
		header("Content-disposition: attachment; filename=$filename");
		header("Content-type: text/csv");

	}

	static function png_headers( $modifyTime = 0, $expires = NULL) {

		if ( is_null( $expires)) $expires = \config::$IMG_EXPIRE_TIME;

		self::_common_headers( $modifyTime, $expires);
		header("Content-type: image/png");

	}

	static function jpg_headers( $modifyTime = 0, $expires = NULL) {

		if ( is_null( $expires)) $expires = \config::$IMG_EXPIRE_TIME;

		self::_common_headers( $modifyTime, $expires);
		header("Content-type: image/jpeg");

	}

	static function gif_headers( $modifyTime = 0, $expires = NULL) {

		if ( is_null( $expires)) $expires = \config::$IMG_EXPIRE_TIME;

		self::_common_headers( $modifyTime, $expires);
		header("Content-type: image/gif");

	}

	static function pdf_headers( $filename = NULL, $modifyTime = 0) {
		self::_common_headers( $modifyTime);
		header("Content-type: application/pdf");
		if ( is_null( $filename))
			$filename = 'pdf-' . date( 'Y-m-d') . '.pdf';

		header( sprintf( 'Content-Disposition: inline; filename="%s"', $filename));

	}

	static function exe_headers( $filename = NULL, $modifyTime = 0) {
		self::_common_headers( $modifyTime);
		header("Content-type: application/octet-stream");
		if ( is_null( $filename))
			$filename = 'binary-' . date( 'Y-m-d') . '.bin';

		header( sprintf( 'Content-Disposition: attachment; filename="%s"', $filename));

	}

	static function xml_docType() {
		return ( "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" . html_docType() );

	}

	protected static  function _common_headers( $modifyTime = 0, $expires = 0) {
		if ( $modifyTime) {
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $modifyTime) . ' GMT');
			if ( $expires) {
				header('Expires: ' . gmdate( 'D, j M Y H:i:s', time() + $expires ) . ' GMT' );

			}
			else {
				header('Expires: ' . gmdate( 'D, j M Y H:i:s' ) . ' GMT' );    	// Date in the past
				header('Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate');
				header('Pragma: no-cache');                          			// HTTP/1.0

			}

		}
		else {
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');	// always modified
			header('Expires: ' . gmdate( 'D, j M Y H:i:s' ) . ' GMT' );    	// Date in the past
			header('Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate');
			header('Pragma: no-cache');                          			// HTTP/1.0

		}

	}

}
