<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/
NameSpace dvc;

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

	public static function get( $var = '', $default = FALSE ) {
		if ( !isset( self::$instance ))
			self::$instance = new Request();

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

		if ( isset( $_GET['url']))
			$this->url = trim( $_GET['url'] ,'/');
		else
			$this->url = $this->uri;

		$url = filter_var( $this->url, FILTER_SANITIZE_URL);
		$this->segments = explode('/', $url);

	}

	public function ReWriteBase() {
		return ( $this->_RewriteBase);

	}

	public function SetReWriteBase( $htaccess) {
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

	public function getPost( $name = '', $default = FALSE ){
		if( !$name)
			return $this->post;

		if( isset($this->post[$name]))
			return $this->post[$name];

		return ( $default );

	}

	public function getQuery($name = ''){
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

	public function getServer($name){
		if(isset($_SERVER[$name]))
			return $_SERVER[$name];

		return '';
	}

	public function getRemoteIP(){
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			return $_SERVER['HTTP_X_FORWARDED_FOR'];

		if ( isset( $_SERVER['REMOTE_ADDR']))
			return $_SERVER['REMOTE_ADDR'];

		return '0.0.0.0';

	}

	public function getServerIP(){
		if ( isset( $_SERVER['SERVER_ADDR']))
			return $_SERVER['SERVER_ADDR'];

		return '0.0.0.0';

	}

	protected function getSubNet( $ip) {
		$a = explode( '.', $ip);
		return ( sprintf( '%d.%d.%d', $a[0], $a[1], $a[2]));

	}

	public function ClientIsLocal() {
		$thisIP = $this->getServerIP();
		$remoteIP = $this->getRemoteIP();

		$thisSubNet = self::getSubNet( $thisIP);
		$remoteSubNet = self::getSubNet( $remoteIP);

		//~ \sys::logger( sprintf( '%s/%s :: %s/%s', $thisIP, $thisSubNet, $remoteIP, $remoteSubNet));

		return ( $thisSubNet == $remoteSubNet);

	}

	public function getReferer(){
		if( isset($_SERVER['HTTP_REFERER']))
			return $_SERVER['HTTP_REFERER'];

		return url::$URL;

	}

	public function DocumentRoot(){
		$root = '';
		if( isset($_SERVER['DOCUMENT_ROOT'])) {
			$root = $_SERVER['DOCUMENT_ROOT'];

			if ( ! preg_match( '@/$@', $root ))
				$root .= '/';


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

	public function getParam($name = '', $default = FALSE){
		if( !$name)
			return $this->params;

		if( isset($this->params[$name])){
			//~ error_log( "Request.php : " . $name . '=' . $this->params[$name]);
			return $this->params[$name];

		}

		return ( $default);

	}

}