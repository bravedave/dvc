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
	protected $url_controller = null;
	protected $url_action = null;
	protected $url_parameter_1 = null;
	protected $url_parameter_2 = null;
	protected $url_parameter_3 = null;
	protected $_app_executed = FALSE;

	protected $url_served = '';
	public $exclude_from_sitemap = FALSE;

	public function app_executed() {
		return $this->_app_executed;

	}

	protected $rootPath = null;

	protected static $_request = null;

	protected $_timer = null;

	protected $db = FALSE;

	public $defaultController = NULL;

	protected $minimum = FALSE;
	protected $service = FALSE;

	protected static $instance = NULL;

	const use_full_url = TRUE;

	static $debug = FALSE;

	static function app() {
		return ( self::$instance);

	}

	static function timer() {
		if ( self::$instance)
			return ( self::$instance->_timer);

		return ( new timer());

	}

	static function Request() {
		if ( is_null( self::$_request))
			self::$_request = Request::get();

		return ( self::$_request);

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

		\config::initialize();	// this initializes config

		$tz = \config::$TIMEZONE;
		$mailserver = \config::$MAILSERVER;

		ini_set ('date.timezone', $tz);
		ini_set ('SMTP', $mailserver);

		$_url = trim( self::Request()->getUrl(), '/. ');
		if (preg_match('/\.(?:png|ico|jpg|jpeg|gif|css|js|orf|eot|svg|ttf|woff|woff2|map|json|txt|xml)(\?.*)?$/', $_url)) {
			/*
			 * You are only here because
			 *	the file was not found in the <webroot>
			 *	this may be a public document
			 */

			// remove the tail after ?
			$_url = preg_replace( '@(\?.*)?$@', '', $_url);

			// check for any .. in the string, which could lead to a parent folder
			if ( strpos( $_url, '..') !== FALSE)
				throw new Exceptions\SecurityException;

			// sanitize, noting that it may have / in the string, and that's ok because leading /. have been removed
			$_url = preg_replace( '@[^a-zA-Z0-9\_\-\./]@', '', $_url);

			if ( $this->publicFile( $_url))
				return;

		}

		$this->db = \sys::dbi();
		if ( $this->minimum) {
			if ( self::$debug) \sys::logger( 'exit: I am minimum');
			return;	// job done

		}

		$this->countVisit();

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
			throw new Exceptions\CannotLocateController;

		self::Request()->setControllerName( $this->url_controller);

		/*
		* Quiet Security - some actions are protected
		* from outside calling, don't broadcast the error
		*/
		$_protectedActions = [
			'__construct',
			'__destruct',
			'authorize',
			'before',
			'dbResult',
			'dbEscape',
			'getParam',
			'hasView',
			'getView',
			'loadView',
			'load',
			'init',
			'page',
			'render',
			'isPost',
			'getPost',
			'sql'
		];

		if ( in_array( strtolower( $this->url_action), $_protectedActions)) {
			\sys::logger( sprintf( 'protecting action %s => %s', $this->url_action, 'index'));
			$this->url_action = 'index';

		}
		// else {
		// 	\sys::logger( sprintf( 'unprotected action %s', $this->url_action));
		//
		// }

		self::Request()->setActionName( $this->url_action);

		require $controllerFile;

		$url_controller_name = $this->url_controller;
		$this->url_controller = new $this->url_controller( $this->rootPath );
		$this->url_controller->name = $url_controller_name;
		$this->url_controller->timer = $this->_timer;

		$this->url_controller->init( $url_controller_name);

		/**
		 * Between here and the end of this function the application will execute
		 *
		 * check for method: does such a method exist in the controller ?
		 */
		if ( method_exists($this->url_controller, $this->url_action)) {

			$this->url_served = sprintf( '%s%s%s/%s', url::$PROTOCOL,
				url::$URL,
				self::Request()->getControllerName(),
				self::Request()->getActionName());

			// call the method and pass the arguments to it
			if (isset($this->url_parameter_3)) {
				// will translate to something like $this->home->method($param_1, $param_2, $param_3);
				if ( self::$debug) \sys::logger( sprintf( '%s->{%s}(%s, %s, %s)',
				 	$this->url_controller->name,
					$this->url_action,
					$this->url_parameter_1,
					$this->url_parameter_2,
					$this->url_parameter_3));

				$this->url_controller->{$this->url_action}(
					$this->url_parameter_1,
					$this->url_parameter_2,
					$this->url_parameter_3);

			}
			elseif (isset($this->url_parameter_2)) {

				if ( self::$debug) \sys::logger( sprintf( '%s->{%s}(%s, %s)',
					$this->url_controller->name,
					$this->url_action,
					$this->url_parameter_1,
					$this->url_parameter_2));

				// will translate to something like $this->home->method($param_1, $param_2);
				$this->url_controller->{$this->url_action}(
					$this->url_parameter_1,
					$this->url_parameter_2);

			}
			elseif (isset($this->url_parameter_1)) {

				if ( self::$debug) \sys::logger( sprintf( '%s->{%s}(%s)',
					$this->url_controller->name,
					$this->url_action,
					$this->url_parameter_1));

				// will translate to something like $this->home->method($param_1);
				$this->url_controller->{$this->url_action}($this->url_parameter_1);

			}
			else {
				if ( self::$debug) \sys::logger( sprintf( '%s->{%s}()',
				 	$this->url_controller->name,
					$this->url_action));

				/**
				 * if no parameters given, just call the
				 * method without parameters,
				 * like $this->home->method();
				 */
				$this->url_controller->{$this->url_action}();

			}

		}
		else {
			$this->url_served = sprintf( '%s%s%s', url::$PROTOCOL, url::$URL, self::Request()->getControllerName());

			if ( self::$debug) \sys::logger( 'fallback');
			if ( self::$debug) \sys::logger( sprintf( '%s->index(%s)', $this->url_controller->name, $this->url_action));

			$this->url_controller->index( $this->url_action);

		}

		$this->_app_executed = TRUE;

	}

	protected function publicFile( $_url) {
		$_file = sprintf( '%s/app/public/%s', $this->rootPath, $_url);
		if ( self::$debug) \sys::logger( sprintf( 'looking for :: %s', $_file));
		if ( file_exists( $_file)) {
			$this->url_served = url::$PROTOCOL . url::$URL . self::Request()->getUrl();
			$this->serve( $_file);
			return true;

		}

		$_file = sprintf( '%s/public/%s', $this->rootPath, $_url);
		if ( self::$debug) \sys::logger( sprintf( 'looking for :: %s', $_file));
		if ( file_exists( $_file)) {
			\sys::logger( sprintf( 'DEPRECATED FILE LOCATION :: %s', $_file));
			\sys::logger( sprintf( 'Please use app/public :: %s', $_file));
			$this->url_served = url::$PROTOCOL . url::$URL . self::Request()->getUrl();
			$this->serve( $_file);
			return true;

		}

		/* this is a system level document - this is the core distribution javascript */
		$_file = sprintf( '%s/public/%s', __DIR__, $_url);
		if ( self::$debug) \sys::logger( sprintf( 'looking for :: %s', $_file));
		if ( file_exists( $_file)) {
			$this->url_served = url::$PROTOCOL . url::$URL . self::Request()->getUrl();
			$this->serve( $_file);
			return true;

		}

		// ok, can't find it - perhaps serve a blank if it is an image
		if (preg_match('/\.(?:png|jpg|jpeg|gif)(\?.*)?$/', $_url)) {

			//~ self::$debug = TRUE;

			if (preg_match('/\.(?:png)(\?.*)?$/', $_url))
				$_file = sprintf( '%s/public/images/1x1.png', __DIR__);
			elseif (preg_match('/\.(?:jpg|jpeg)(\?.*)?$/', $_url))
				$_file = sprintf( '%s/public/images/1x1.jpg', __DIR__);
			elseif (preg_match('/\.(?:gif)(\?.*)?$/', $_url))
				$_file = sprintf( '%s/public/images/1x1.gif', __DIR__);

			if ( file_exists( $_file)) {
				if ( self::$debug) \sys::logger( sprintf( 'file not found : %s : serving :: %s', $_url, $_file));
				$this->url_served = url::$PROTOCOL . url::$URL . self::Request()->getUrl();
				$this->serve( $_file);
				return true;

			}

		}

	}

	public function countVisit() {}

	protected function serve( $path ) {
		if ( self::$debug) sys::$debug = TRUE;
		sys::serve( $path);

	}

	protected function splitUrl() {
		/**
		* Get and split the URL
		*/
		$url = self::Request()->getUrl();
		if ( self::Request()->ReWriteBase() != '' && '/' . $url == self::Request()->ReWriteBase()) {
			if ( self::$debug) \sys::logger( sprintf( 'ReWriteBase = %s', Request::get()->ReWriteBase()));
			$url = '';

		}

		if ( $url != "" ) {
			if ( self::$debug) \sys::logger( 'Url: ' . $url);

			// split URL
			$url = self::Request()->getSegments();

			// Put URL parts into according properties
			$this->url_controller = self::Request()->getSegment(0);
			$this->url_action = self::Request()->getSegment(1);
			$this->url_parameter_1 = self::Request()->getSegment(2);
			$this->url_parameter_2 = self::Request()->getSegment(3);
			$this->url_parameter_3 = self::Request()->getSegment(4);

			// turn debug on if you have problems with the URL
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

	public function getInstallPath() {
		return ( realpath( __DIR__ . '/../'));

	}

	public function return_url() {
		return ( $this->url_served);
	}

	public function __destruct() {
		if ( $this->app_executed()) {
			if ( config::$SITEMAPS ) {
				$path = $this->return_url();

				try {
					$dao = new dao\sitemap;
					if ( $dto = $dao->getDTObyPath( $path)) {
						$this->db->Q( sprintf( 'UPDATE sitemap SET visits = visits + 1 WHERE id = %d', (int)$dto->id));


					}
					else {
						//~ sys::logger( 'not found path : ' . $path);
						$a = [
							'path' => $path,
							'visits' => 1,
							'exclude_from_sitemap' => ((bool)$this->exclude_from_sitemap ? 1 : 0 )
						];

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
