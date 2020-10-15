<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc;

abstract class Response {

	protected static function _common_headers( $modifyTime = 0, $expires = 0) {
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

	static function css_headers( $modifyTime = 0, $expires = null) {

		if ( is_null( $expires)) $expires = \config::$CSS_EXPIRE_TIME;

		self::_common_headers( $modifyTime, $expires);
		header('Content-type: text/css');

	}

	static function csv_headers( $filename = "download.csv", $modifyTime = 0, $expires = 0) {
		self::_common_headers( $modifyTime, $expires);
		header("Content-Description: File Transfer");
		header("Content-disposition: attachment; filename=$filename");
		header("Content-type: text/csv");

	}

	static function excel_headers( $filename = "download.xml" ) {
		self::_common_headers();
		header("Content-Description: File Transfer");
		header("Content-Disposition: attachment; filename=$filename");
		header("Content-type: application/vnd.ms-excel");
	}

	static function exe_headers( $filename = null, $modifyTime = 0) {
		self::_common_headers( $modifyTime);
		header("Content-type: application/octet-stream");
		if ( is_null( $filename))
			$filename = 'binary-' . date( 'Y-m-d') . '.bin';

		header( sprintf( 'Content-Disposition: attachment; filename="%s"', $filename));

	}

	static function headers( $mimetype, $modifyTime = 0, $expires = 0) {
		self::_common_headers( $modifyTime, $expires);
		header( sprintf( 'Content-type: %s', $mimetype));

	}

	static function html_docType() {
		$docType = ( userAgent::isIE() ? 'html4' : 'html5' );

		if ( $docType == 'html5' ) {
			if ( userAgent::isMobileDevice()) {
				return ( "<!DOCTYPE html>\n<html lang=\"en\">" );

			}
			else {
				return ( "<!DOCTYPE html>\n<html class=\"desktop\" lang=\"en\">" );

			}

		}
		else {
			return ( "<!DOCTYPE html
		PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"
		\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">" );

		}

	}

	static function html_headers( $charset = false) {
		if ( !$charset)
			$charset = 'UTF-8';

		Response::_common_headers();

		if ( \config::$CONTENT_SECURITY_ENABLED)
			header( "Content-Security-Policy: frame-ancestors 'self'");

		header( sprintf( "Content-type: text/html; charset=%s", $charset));

	}

	static function gif_headers( $modifyTime = 0, $expires = null) {

		if ( is_null( $expires)) $expires = \config::$IMG_EXPIRE_TIME;

		self::_common_headers( $modifyTime, $expires);
		header("Content-type: image/gif");

	}

	static function icon_headers( $modifyTime = 0, $expires = null) {

		if ( is_null( $expires)) $expires = \config::$IMG_EXPIRE_TIME;

		self::_common_headers( $modifyTime, $expires);
		header('Content-type: image/x-icon');

	}

	static function javascript_headers( $modifyTime = 0, $expires = 0) {
		self::_common_headers( $modifyTime, $expires);
		header('Content-type: text/javascript');

	}

	static function jpg_headers( $modifyTime = 0, $expires = null) {

		if ( is_null( $expires)) $expires = \config::$IMG_EXPIRE_TIME;

		self::_common_headers( $modifyTime, $expires);
		header("Content-type: image/jpeg");

	}

	static function json_headers( $modifyTime = 0, $length = 0) {
		self::_common_headers( $modifyTime);
		header('Content-type: application/json');
		if ( $length) header( sprintf('Content-length: %s', $length));

	}

	static function mso_docType() {
		return '<html xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns:m="http://schemas.microsoft.com/office/2004/12/omml" xmlns="http://www.w3.org/TR/REC-html40">';

	}

	static function pdf_headers( $filename = null, $modifyTime = 0) {
		self::_common_headers( $modifyTime);
		header('Content-type: application/pdf');
		if ( is_null( $filename))
			$filename = 'pdf-' . date( 'Y-m-d') . '.pdf';

		header( sprintf( 'Content-Disposition: inline; filename="%s"', $filename));

	}

	static function png_headers( $modifyTime = 0, $expires = null) {

		if ( is_null( $expires)) $expires = \config::$IMG_EXPIRE_TIME;

		self::_common_headers( $modifyTime, $expires);
		header("Content-type: image/png");

	}

	static function redirect( $url = null, $message = "", $auto = true ) {

		if ( is_null( $url )) {
			$url = \url::$URL;	// default

		}
		elseif ( ! ( preg_match( '@^(http|//)@i', (string)$url ))) {
			if ( '/' != $url)
				$url = \url::$URL . $url;

		}

		if ( $message == "" ) {
			header( sprintf( 'location: %s', $url));
			exit;

		}

		\dvc\pages\page::$MainContextMenu = false;

		$p = new \dvc\pages\bootstrap4;
			$p->title = $message;
			$p->footer = false;
			$p->additionalScripts = [];
		if ( userAgent::isMobileDevice())
			$p->meta[] = '<meta name="viewport" content="initial-scale=1" />';

		if ( $auto ) {
			$p->header( false);
			printf( '<meta http-equiv="refresh" content="1; url=%s" />%s</head><body>',
				$url,
				PHP_EOL);

		}
		else {
			$p->header();

		}

		print '<div style="margin: 50px auto 10px auto; padding: 10px; border: 1px solid silver; max-width: 600px;">';

		printf( '<p style="margin-top: 15px; margin-bottom: 15px;">%s</p>

		<div style="text-align: right; padding-right: 20x;">
			<a style="text-decoration: none; font-style: italic;" href="%s">%s .... .</a>

		</div>', $message,  $url, ( $auto ? 'redirecting' : 'continue' ));

		print '</div>';

		exit;	// don't run anything else

	}

	static function text_headers( $modifyTime = 0, $expires = 0) {
		self::_common_headers( $modifyTime, $expires);
		header("Content-type: text/plain");

	}

	static function tiff_headers( $filename = null, $modifyTime = 0) {
		self::_common_headers( $modifyTime);
		header("Content-type: image/tiff");
		if ( is_null( $filename))
			$filename = 'binary-' . date( 'Y-m-d') . '.tiff';

		header( sprintf( 'Content-Disposition: inline; filename="%s"', $filename));

	}

	static function xml_docType() {
		return ( "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" . html_docType() );

	}

	static function xml_headers( $modifyTime = 0) {
		Response::_common_headers( $modifyTime);
		header('Content-type: text/xml');

	}

	static function zip_headers( $filename = null, $modifyTime = 0) {
		self::_common_headers( $modifyTime);
		header("Content-type: application/zip");
		if ( is_null( $filename))
			$filename = 'binary-' . date( 'Y-m-d') . '.zip';

		header( sprintf( 'Content-Disposition: attachment; filename="%s"', $filename));

	}

}
