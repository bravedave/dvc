<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/
NameSpace dvc;
Use \dao;

define( 'APPLICATION', 1 );

class _application {
	/** @var null The controller */
	private $url_controller = null;

	/** @var null The method (of the above controller), often also named "action" */
	private $url_action = null;

	/** @var null Parameter one */
	private $url_parameter_1 = null;

	/** @var null Parameter two */
	private $url_parameter_2 = null;

	/** @var null Parameter three */
	private $url_parameter_3 = null;

	private $url_served = '';

	public $exclude_from_sitemap = FALSE;

	protected $_app_executed = FALSE;

	public function app_executed() {
		return $this->_app_executed;

	}

	private $rootPath = null;

	private $Request = null;

	protected $_timer = null;

	private $db = FALSE;

	public $defaultController = NULL;

	protected $minimum = FALSE;
	protected $service = FALSE;

	protected static $instance = NULL;

	protected static $debug = FALSE;

	static function app() {
		return ( self::$instance);

	}

	static function timer() {
		if ( self::$instance)
			return ( self::$instance->_timer);

		return ( new timer());

	}

	/**
	* "Start" the application:
	* Analyze the URL elements and calls the according controller/method or the fallback
	*/
	public function __construct( $rootPath ) {
		self::$instance = $this;

		$this->rootPath = realpath( $rootPath);
		if ( self::$debug) \sys::logger( sprintf( 'rootpath :: %s', $this->rootPath ));
		sys::set_error_handler();

		$this->_timer = new timer();

		$tz = config::$TIMEZONE;	// this initializes config
		$mailserver = config::$MAILSERVER;

		ini_set ('date.timezone', $tz);
		ini_set ('SMTP', $mailserver);

		$this->Request = Request::get();
		if (preg_match('/\.(?:png|ico|jpg|jpeg|gif|css|js|orf|eot|svg|ttf|woff|woff2|map|json|txt|xml)$/', $this->Request->getUrl())) {
			/*
			 * You are only here because
			 *	the file was not found in the <webroot>
			 *	this may be a public document
			 */
			if ( file_exists( sprintf( '%s/app/public/%s', $this->rootPath, $this->Request->getUrl() ))) {
				$this->url_served = url::$PROTOCOL . url::$URL . $this->Request->getUrl();
				$this->serve( sprintf( '%s/app/public/%s', $this->rootPath, $this->Request->getUrl()));
				return;

			}

			if ( file_exists( sprintf( '%s/public/%s', $this->rootPath, $this->Request->getUrl() ))) {
				$this->url_served = url::$PROTOCOL . url::$URL . $this->Request->getUrl();
				$this->serve( sprintf( '%s/public/%s', $this->rootPath, $this->Request->getUrl()));
				return;

			}

			/* this would be a system level document - this is the core distribution javascript */
			if ( file_exists( sprintf( '%s/public/%s', __DIR__, $this->Request->getUrl() ))) {
				$this->url_served = url::$PROTOCOL . url::$URL . $this->Request->getUrl();
				$this->serve( sprintf( '%s/public/%s', __DIR__, $this->Request->getUrl()));
				return;

			}

		}

		$this->db = dbi::getDBI();
		if ( $this->minimum) {
			if ( self::$debug) \sys::logger( 'exit: I am minimum');
			return;	// job done

		}



		$this->splitUrl();		// create array with URL parts in $url
		/*
		 * example: if controller would be "car",
		 * then this line would translate into: $this->car = new car();
		 */
		if ( is_null( $this->defaultController ))
			$this->defaultController = config::$DEFAULT_CONTROLLER;
		if ( trim( $this->url_controller == '' ))
			$this->url_controller = $this->defaultController;

		$controllerFile = $this->rootPath . '/controller/' . $this->url_controller . '.php';

		/*---[ check for controller: does such a controller exist ? ]--- */

		if ( !file_exists( $controllerFile)) {
			$controllerFile = __DIR__ . '/controller/' . $this->url_controller . '.php';	// is there a default controller for this action
			if ( self::$debug) \sys::logger( 'checking for system default controller : ' . $controllerFile);

		}

		if ( !file_exists( $controllerFile)) {
			$controllerFile = $this->rootPath . '/controller/' . $this->defaultController . '.php';				// invalid URL, so show home/index
			if ( !file_exists( $controllerFile)) {
				$controllerFile = $this->rootPath . '/controller/' . config::$DEFAULT_CONTROLLER . '.php';	// invalid URL, so home/index
				if ( !file_exists( $controllerFile)) {
					$controllerFile = __DIR__ . '/controller/' . config::$DEFAULT_CONTROLLER . '.php';		// invalid URL, so system home/index
					if ( self::$debug) \sys::logger( 'checking for system default controller (deep)');

				}
				else {
					if ( self::$debug) \sys::logger( 'default controller');

				}

			}
			else {
				if ( self::$debug) \sys::logger( 'default controller');

			}

			if ( $this->url_controller != '' ) {
				if ( self::$debug) \sys::logger( 'bumped controller => action');
				$this->url_parameter_3 = $this->url_parameter_2;
				$this->url_parameter_2 = $this->url_parameter_1;
				$this->url_parameter_1 = $this->url_action;
				$this->url_action = $this->url_controller;	// bump

			}
			$this->url_controller = $this->defaultController;

		}

		if ( $this->service) {
			if ( self::$debug) \sys::logger( 'exit: I am a service');
			return;	// job done

		}

		if ( !file_exists( $controllerFile))
			die( 'cannot locate the controller file');

		require $controllerFile;

		$url_controller_name = $this->url_controller;
		$this->url_controller = new $this->url_controller( $this->rootPath );
		$this->url_controller->name = $url_controller_name;
		$this->url_controller->Request = $this->Request;
		$this->url_controller->timer = $this->_timer;
		$this->url_controller->init( $url_controller_name);

		$this->Request->setControllerName($url_controller_name);
		$this->Request->setActionName($this->url_action);

		/*
		 * Between here and the end of this function the application will execute
		 *
		 * check for method: does such a method exist in the controller ? */
		if ( method_exists($this->url_controller, $this->url_action)) {

			$this->url_served = sprintf( '%s%s%s/%s', url::$PROTOCOL, url::$URL, $this->Request->getControllerName(), $this->Request->getActionName());

			// call the method and pass the arguments to it
			if (isset($this->url_parameter_3)) {
				// will translate to something like $this->home->method($param_1, $param_2, $param_3);
				if ( self::$debug)  \sys::logger( sprintf( '%s->{%s}(%s, %s, %s)', $this->url_controller->name, $this->url_action, $this->url_parameter_1, $this->url_parameter_2, $this->url_parameter_3 ));

				$this->url_controller->{$this->url_action}($this->url_parameter_1, $this->url_parameter_2, $this->url_parameter_3);

			}
			elseif (isset($this->url_parameter_2)) {

				if ( self::$debug) \sys::logger( sprintf( '%s->{%s}(%s, %s)', $this->url_controller->name, $this->url_action, $this->url_parameter_1, $this->url_parameter_2 ));

				// will translate to something like $this->home->method($param_1, $param_2);
				$this->url_controller->{$this->url_action}($this->url_parameter_1, $this->url_parameter_2);

			}
			elseif (isset($this->url_parameter_1)) {

				if ( self::$debug) \sys::logger( sprintf( '%s->{%s}(%s)', $this->url_controller->name, $this->url_action, $this->url_parameter_1 ));

				// will translate to something like $this->home->method($param_1);
				$this->url_controller->{$this->url_action}($this->url_parameter_1);

			}
			else {
				if ( self::$debug) \sys::logger( sprintf( '%s->{%s}()', $this->url_controller->name, $this->url_action ));

				// if no parameters given, just call the method without parameters, like $this->home->method();
				$this->url_controller->{$this->url_action}();

			}

		}
		else {
			$this->url_served = sprintf( '%s%s%s', url::$PROTOCOL, url::$URL, $this->Request->getControllerName());

			if ( self::$debug) \sys::logger( 'fallback');
			if ( self::$debug) \sys::logger( sprintf( '%s->index(%s)', $this->url_controller->name, $this->url_action ));

			// default/fallback: call the index() method of a selected controller
			//~ $this->exclude_from_sitemap = TRUE;
			//~ sys::logger( sprintf( 'excluded from exclude_from_sitemap (fallback) => %s/%s', $this->Request->getControllerName(), $this->Request->getActionName()));
			$this->url_controller->index( $this->url_action );
			//~ sys::logger( sprintf( '%s - %s', $this->url_controller->name, $this->url_action ));


		}

		$this->_app_executed = TRUE;

	}

	protected function serve( $path ) {
		$path_parts = pathinfo( $path);
		$ext = strtolower( $path_parts['extension']);
		//~ echo $path_parts['dirname'], "\n";
		//~ echo $path_parts['basename'], "\n";
		//~ echo $path_parts['filename'], "\n"; // since PHP 5.2.0

		if ( $ext == "css" ) {
			Response::css_headers( filemtime( $path));
			readfile( $path);
			\sys::logger( "served: $path", 3 );

		}
		elseif ( $ext == "js" ) {
			$expires = 0;
			if ( strstr( $path, 'jquery-'))
				$expires = \config::$JQUERY_EXPIRE_TIME;
			elseif ( strstr( $path, 'inputosaurus.js'))
				$expires = \config::$JQUERY_EXPIRE_TIME;
			elseif ( strstr( $path, 'tinylib.js'))
				$expires = \config::$JQUERY_EXPIRE_TIME;
			elseif ( strstr( $path, 'moment.min.js'))
				$expires = \config::$JQUERY_EXPIRE_TIME;

			Response::javascript_headers( filemtime( $path), $expires);
			readfile( $path);
			if ( self::$debug) \sys::logger( "served: $path");

		}
		elseif ( $ext == "ico" ) {
			Response::icon_headers( filemtime( $path), \config::$CORE_IMG_EXPIRE_TIME);
			readfile( $path);
			if ( self::$debug) \sys::logger( "served: $path");

		}
		elseif ( $ext == "woff" || $ext == "woff2" ) {
			Response::headers('application/font-woff', filemtime( $path), \config::$FONT_EXPIRE_TIME);
			readfile( $path);
			if ( self::$debug) \sys::logger( "served: $path");

		}
		elseif ( $ext == "ttf" || $ext == "otf" ) {
			Response::headers('application/font-sfnt', filemtime( $path), \config::$FONT_EXPIRE_TIME);
			readfile( $path);
			if ( self::$debug) \sys::logger( "served: $path");

		}
		elseif ( $ext == "eot" ) {
			Response::headers('application/vnd.ms-fontobject', filemtime( $path), \config::$FONT_EXPIRE_TIME);
			readfile( $path);
			if ( self::$debug) \sys::logger( "served: $path");

		}
		elseif ( $ext == "png" ) {
			if ( strstr( $path, url::$URL . 'images/'))
				Response::png_headers( filemtime( $path), \config::$CORE_IMG_EXPIRE_TIME);
			else
				Response::png_headers( filemtime( $path));

			readfile( $path);
			if ( self::$debug) \sys::logger( "served: $path");

		}
		elseif ( $ext == "jpg" ) {
			if ( strstr( $path, url::$URL . 'images/'))
				Response::jpg_headers( filemtime( $path), \config::$CORE_IMG_EXPIRE_TIME);
			else
				Response::jpg_headers( filemtime( $path));
			readfile( $path);
			if ( self::$debug) \sys::logger( "served: $path");

		}
		elseif ( $ext == "gif" ) {
			if ( strstr( $path, url::$URL . 'images/'))
				Response::gif_headers( filemtime( $path), \config::$CORE_IMG_EXPIRE_TIME);
			else
				Response::gif_headers( filemtime( $path));
			readfile( $path);
			if ( self::$debug) \sys::logger( "served: $path");

		}
		elseif ( $ext == "svg" ) {
			Response::headers('image/svg+xml', filemtime( $path));
			readfile( $path);
			if ( self::$debug) \sys::logger( "served: $path");

		}
		elseif ( $ext == "map" ) {
			Response::text_headers( filemtime( $path));
			readfile( $path);
			if ( self::$debug) \sys::logger( "served: $path" );

		}
		elseif ( $ext == "json" ) {
			Response::json_headers( filemtime( $path));
			readfile( $path);
			if ( self::$debug) \sys::logger( "served: $path");

		}
		elseif ( $ext == "txt" ) {
			Response::text_headers( filemtime( $path));
			readfile( $path);
			if ( self::$debug) \sys::logger( "served: $path");

		}
		elseif ( $ext == "xml" ) {
			Response::xml_headers( filemtime( $path));
			readfile( $path);
			if ( self::$debug) \sys::logger( "served: $path");

		}
		else {
			if ( self::$debug) \sys::logger( "not serving: $path" );

		}

	}

	private function splitUrl() {
		/**
		* Get and split the URL
		*/
		$url = $this->Request->getUrl();
		if ( $this->Request->ReWriteBase() != '' && '/' . $url == $this->Request->ReWriteBase()) {
			if ( self::$debug) \sys::logger( sprintf( 'ReWriteBase = %s', Request::get()->ReWriteBase()));
			$url = '';

		}

		if ( $url != "" ) {
			if ( self::$debug) \sys::logger( 'Url: ' . $url);

			// split URL
			//~ $url = filter_var($url, FILTER_SANITIZE_URL);
			//~ $url = explode('/', $url);
			$url = $this->Request->getSegments();

			// Put URL parts into according properties
			//~ $this->url_controller = (isset($url[0]) ? $url[0] : null);
			//~ $this->url_action = (isset($url[1]) ? $url[1] : null);
			//~ $this->url_parameter_1 = (isset($url[2]) ? $url[2] : null);
			//~ $this->url_parameter_2 = (isset($url[3]) ? $url[3] : null);
			//~ $this->url_parameter_3 = (isset($url[4]) ? $url[4] : null);

			$this->url_controller = $this->Request->getSegment(0);
			$this->url_action = $this->Request->getSegment(1);
			$this->url_parameter_1 = $this->Request->getSegment(2);
			$this->url_parameter_2 = $this->Request->getSegment(3);
			$this->url_parameter_3 = $this->Request->getSegment(4);

			// for debugging. uncomment this if you have problems with the URL
			if ( self::$debug) \sys::logger( 'Controller: ' . $this->url_controller);
			if ( self::$debug) \sys::logger( 'Action: ' . $this->url_action);
			if ( self::$debug) \sys::logger( 'Parameter 1: ' . $this->url_parameter_1);
			if ( self::$debug) \sys::logger( 'Parameter 2: ' . $this->url_parameter_2);
			if ( self::$debug) \sys::logger( 'Parameter 3: ' . $this->url_parameter_3);

		}

	}


	public function controller() {
		if ( is_string( $this->url_controller))
			return ( $this->url_controller );

		elseif ( isset( $this->url_controller))
			return ( $this->url_controller->name );

		return ('');

	}

	public function dbi() {
		return ( $this->db );

	}

	public function getRootPath() {
		return ( $this->rootPath );

	}

	public function return_url() {
		return ( $this->url_served);
	}

	public function __destruct() {
		if ( $this->app_executed()) {
			if ( config::$SITEMAPS ) {
				$path = $this->return_url();

				try {
					$dao = new dao\sitemap();
					if ( $dto = $dao->getDTObyPath( $path)) {
						//~ sys::logger( 'found path : ' . $path);
						$this->db->Q( 'UPDATE sitemap SET visits = visits + 1 WHERE id = ' . (int)$dto->id);


					}
					else {
						//~ sys::logger( 'not found path : ' . $path);
						$a = array( 'path' => $path,
							'visits' => 1,
							'exclude_from_sitemap' => ((bool)$this->exclude_from_sitemap ? 1 : 0 ));

						$this->db->Insert( 'sitemap', $a);

					}

				}
				catch ( \Exception $e) {
					error_log( $e->getMessage());

				}

			}

		}

	}

}
