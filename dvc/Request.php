<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/
namespace dvc;

class Request {
	protected $controllerName = 'Home';
	protected $actionName = '';
	protected $uri = '';
	protected $_RewriteBase = '';
	protected $url;
	protected $post;
	protected $query;
	protected $params;
	protected $segments;

	protected static $instance;

	public static function get( $var = '', $default = false ) {
		if ( !isset( self::$instance ))
			self::$instance = new Request;

		if ( $var == '' )
			return self::$instance;

		return ( self::$instance->getParam( $var, $default ));

	}

	private function __construct(){
		$this->post = $_POST;
		$this->query = $_GET;

		//~ $this->query = array();
		//~ $a = $_GET;
		//~ foreach ( $a as $k => $v)
			//~ $this->query[$k] = urldecode( $v);

		$this->params = array_merge($this->query, $this->post);

		if ( isset(  $_SERVER['REQUEST_URI']))
			$this->uri = trim( $_SERVER['REQUEST_URI'] ,'/');

		if ( isset( $_GET['url'])) {
			\sys::logger( 'DEPRECATION:');
			\sys::logger( ' .htaccess');
			\sys::logger( ' use FallbackResource instead of ReWriteEngine');
			\sys::logger( ' ---');
			\sys::logger( ' RewriteEngine Off');
			\sys::logger( '');
			\sys::logger( ' FallbackResource /_dvc.php');
			\sys::logger( '');

			$this->url = trim( $_GET['url'] ,'/');

		}
		else {
			$this->url = $this->uri;

		}

		$url = filter_var( $this->url, FILTER_SANITIZE_URL);
		$url = preg_replace( '/\?(.*)$/', '', $url);
		if ( $url == 'sitemap.txt')
			$this->segments = ['sitemap','txt'];
		else
			$this->segments = explode('/', $url);

		// sys::logger( $url);
		// $q = []; foreach( $this->query as $k => $v) $q[] = sprintf( '%s=%s', $k, $v);
		// sys::logger( implode(';',$q));

	}

	public function DNT() {
		return ( 1 == (int)$this->getServer('HTTP_DNT'));

	}

	public function ReWriteBase() {
		return ( $this->_RewriteBase);

	}

	public function setReWriteBase( $htaccess) {
		$a = explode( "\n", $htaccess);
		$rwb = '';

		foreach ( $a as $l) {
			if ( preg_match( '@^RewriteBase@', $l)) {
				$rwb = trim( preg_replace( '@^RewriteBase@', '', $l));
				break;

			}

		}

		sys::logger( sprintf( 'set ReWriteBase to :%s:', $rwb), 5 );
		$this->_RewriteBase = $rwb;

	}

	public function setControllerName($controllerName){
		$this->controllerName = $controllerName;

	}

	public function getControllerName(){
		return $this->controllerName;

	}

	public function setActionName($actionName){
		$this->actionName = $actionName;

	}

	public function getActionName(){
		return $this->actionName;

	}

	public function getSegment($index){
		if( isset($this->segments[$index]))
			return $this->segments[$index];

		return (NULL);

	}

	public function getSegments(){
		return $this->segments;

	}

	public function getPost( $name = '', $default = false ){
		if( !$name)
			return $this->post;

		if( isset($this->post[$name]))
			return $this->post[$name];

		return ( $default );

	}

	public function getQuery( $name = ''){
		if(!$name)
			return $this->query;

		if(isset($this->query[$name]))
			return $this->query[$name];

	}

	public function getUri(){
		return ($this->uri);

	}

	public function getUrl(){
		return ($this->url);

	}

	public function getReferer(){
		if( isset($_SERVER['HTTP_REFERER']))
		return $_SERVER['HTTP_REFERER'];

		return url::$URL;

	}

	public function getRemoteIP(){
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			return $_SERVER['HTTP_X_FORWARDED_FOR'];

		if ( isset( $_SERVER['REMOTE_ADDR']))
			return $_SERVER['REMOTE_ADDR'];

		return '0.0.0.0';

	}

	public function getServer( $name){
		if( isset( $_SERVER[$name])) {
			return $_SERVER[$name];

		}

		return '';

	}

	public function getServerName(){
		return $this->getServer('SERVER_NAME');

	}

	public function getServerIP(){
		if ( isset( $_SERVER['SERVER_ADDR']))
			return $_SERVER['SERVER_ADDR'];

		return '0.0.0.0';

	}

	protected static function getSubNet( $ip) {
		$a = explode( '.', $ip);
		return ( sprintf( '%d.%d.%d', $a[0], $a[1], $a[2]));

	}

	public function ServerIsLocal() {
		if ( isset( $_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == 'localhost') {
			return ( true);

		}

		return ( false);

	}

	public function ClientIsLocal() {
		if ( $this->ServerIsLocal())
			return ( true);

		$thisIP = $this->getServerIP();
		$remoteIP = $this->getRemoteIP();

		$thisSubNet = self::getSubNet( $thisIP);
		$remoteSubNet = self::getSubNet( $remoteIP);

		//~ \sys::logger( sprintf( '%s/%s :: %s/%s', $thisIP, $thisSubNet, $remoteIP, $remoteSubNet));

		return ( $thisSubNet == $remoteSubNet);

	}

	public function DocumentRoot(){
		$root = '';
		if( isset($_SERVER['DOCUMENT_ROOT'])) {
			$root = $_SERVER['DOCUMENT_ROOT'];

			if ( ! preg_match( '@/$@', $root )) {
				$root .= '/';

			}

		}

		return $root;

	}

	public function isPost(){
		if($this->getServer('REQUEST_METHOD') == 'POST')
			return true;

		return false;

	}

	public function isGet(){
		if($this->getServer('REQUEST_METHOD') == 'GET')
			return true;

		return false;

	}

	public function toArray(){
		return $this->params;

	}

	public function getParam( $name = '', $default = false){
		if( !$name)
			return $this->params;

		if( isset($this->params[$name])){
			//~ error_log( "Request.php : " . $name . '=' . $this->params[$name]);
			return $this->params[$name];

		}

		return ( $default);

	}

	public function fileUpload( $path, $accept = null) {
		/*
			upload files to path from POST
			*/
		//~ $debug = true;
		$debug = false;

		$response = [
			'response' => 'ack',
			'description' => [],
			'files' => [],

		];

		if ( \is_null($accept)) {
			$accept = [
				'image/png',
				'image/x-png',
				'image/jpeg',
				'image/pjpeg',
				'image/tiff',
				'image/gif',
				'text/plain',
				'application/pdf',
				'application/x-zip-compressed',
				'application/msword',
				'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
				'application/vnd.ms-excel',
				'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

		}

		foreach ( $_FILES as $key => $file ) {
			if ( $debug) sys::logger( sprintf( '/upload: %s : %s', $key, $file['name']));
			if ( $file['error'] == UPLOAD_ERR_INI_SIZE ) {
				if ( $debug) sys::logger( sprintf( 'upload: %s is too large (ini)', $file['name']));
				$response['description'][] = $file['name'] . ' is too large (ini)';
				$response['response'] = 'nak';

			}
			elseif ( $file['error'] == UPLOAD_ERR_FORM_SIZE ) {
				if ( $debug) sys::logger( sprintf( 'upload: %s is too large (form)', $file['name']));
				$response['description'][] = $file['name'] . ' is too large (form)';
				$response['response'] = 'nak';

			}
			elseif ( is_uploaded_file( $file['tmp_name'] )) {
				$strType = $file['type'];
				if ( $debug) sys::logger( sprintf( 'upload: %s (%s)', $file['name'], $strType));

				$ok = true;
				if ( in_array( $strType, $accept)) {
					$source = $file['tmp_name'];
					$target = sprintf( '%s/%s', $path, $file['name']);

					if ( file_exists( $target ))
						unlink( $target );

					if ( move_uploaded_file( $source, $target)) {
						chmod( $target, 0666 );
						$response['description'][] = $file['name'] . ' uploaded';
						$response['files'][$key] = $file['name'];

					}
					else {
						if ( $debug) sys::logger("Possible file upload attack!  Here's some debugging info:\n" . var_export($_FILES, TRUE));

					}

				}
				elseif ( $strType == "" ) {
					if ( $debug) sys::logger( sprintf( 'upload: %s invalid file type', $file['name']));
					$response['description'][] = $file['name'] . ' invalid file type ..';
					$response['response'] = 'nak';

				}
				else {
					if ( $debug) sys::logger( sprintf( 'upload: %s file type not permitted - %s', $file['name'], $strType));
					$response['description'][] = $file['name'] . ' file type not permitted ..: ' . $strType;
					$response['response'] = 'nak';

				}

			}	// elseif ( is_uploaded_file( $file['tmp_name'] )) {
			else {
				if ( $debug) \sys::logger( sprintf( 'not :: is_uploaded_file( %s)', print_r( $file, true)));

			}

		}

		return ( $response);

	}

}
