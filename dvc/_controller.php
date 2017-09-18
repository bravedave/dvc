<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	**
	* This is the "base controller class". All other "real" controllers extend this class.
	*/
NameSpace dvc;
Use dao;

abstract class _controller {
	public $authorized = FALSE;
	public $authorised = FALSE;
	public $RequireValidation = TRUE;
	public $CheckOffline = TRUE;
	public $db = NULL;
	public $name = 'home';
	public $Request = NULL;
	public $timer = NULL;
	public $rootPath  = '';
	public $defaultController = 'home';
	public $title;
	public $debug = FALSE;

	protected $Redirect_OnLogon = FALSE;

	static $url;

	function __construct( $rootPath ) {
		if ( $this->debug) \sys::logger( __FILE__ . ' :: start construct');
		$this->rootPath = $rootPath;
		$this->title = config::$WEBNAME;

		/**
		 * Whenever a controller is created, open a database connection too.
		 * The aim is to have ONE connection that can be used globally.
		 */
		$this->db = application::app()->dbi();

		if ( $this->debug) \sys::logger( __FILE__ . ' :: checking authority');
		$this->authorised = \currentUser::valid();
		if ( $this->debug) \sys::logger( __FILE__ . ' :: checking authority :: ' . ( $this->authorised ? 'private' : 'public'));

		if ( $this->RequireValidation ) {
			if ( !( $this->authorised))
				$this->authorize();

		}

		if ( $this->CheckOffline ) {
			if ( $this->authorised) {
				if ( !\currentUser::isadmin()) {
					$state = new dao\state( $this->db);
					if ( $state->offline())
						Response::redirect( \url::tostring( 'offline'));

				}

			}

		}

		$this->authorized = $this->authorised;	// american spelling accepted (doh)
		$this->before();

	}

	protected function before() {
		/*
		 Inspired by something I read in the fuelPHP documentation
		 this method is called at the end of __connstruct and can
		 be used to modify the _controller class */

	}

	protected function getParam( $v = '', $default = FALSE ) {
		if ( is_null( $this->Request ))
			return ( FALSE);

		return ( $this->Request->getParam( $v, $default));

	}

	protected function isPost() {
		if ( is_null( $this->Request ))
			return ( FALSE);

		return ( $this->Request->isPost());

	}

	protected function getPost( $name = '', $default = FALSE ) {
		if ( is_null( $this->Request ))
			return ( FALSE);

		return ( $this->Request->getPost( $name, $default ));

	}

	protected function authorize() {
		if ( auth::GoogleAuthEnabled()) {
			if ( $this->debug) \sys::logger( 'gauth - test');
			if ( \user::hasGoogleFlag()) {
				\user::clearGoogleFlag();
				if ( $this->debug) \sys::logger( 'gauth');

				Response::redirect( \url::tostring( 'auth/request'));
				return;

			}

		}

		if ( \userAgent::isGoogleBot())
			exit;	// quietly

		if ( $this->Redirect_OnLogon)
			Response::redirect( \url::tostring( sprintf( 'logon?referer=%s', $this->Redirect_OnLogon)));

		else
			Response::redirect( \url::tostring( 'logon'));

	}

	protected function dbResult( $query) {
		if ( is_null( $this->db ))
			return ( FALSE);

		return ( $this->db->Result( $query));

	}

	protected function dbEscape( $s) {
		if ( is_null( $this->db ))
			return ( $s);

		return ( $this->db->escape( $s));

	}

	public function index() {
		$this->page404();

	}

	public function page404() {
		header('HTTP/1.0 404 Not Found');
		$p = new pages\page('404 Not Found');
			$p->content();

			$this->load('not-found');


	}

	protected function hasView( $viewName = 'index', $controller = NULL ) {
		$view = $this->getView( $viewName, $controller);
		return ( file_exists( $view));

	}

	protected function getView( $viewName = 'index', $controller = NULL ) {
		if ( is_null( $controller ))
			$controller = $this->name;

		/*
		 	first look for a php view, then a markdown

		 	first search the application folders
				[application]/views/[controller]
				[application]/app/views/	*/

		if ( preg_match( '/\.(php|md)$/', $viewName)) {		// extension was specified
			$view = sprintf( '%s/views/%s/%s', $this->rootPath, $controller, $viewName );
			if ( file_exists( $view))
				return ( $view);

		}
		else {
			$view = sprintf( '%s/views/%s/%s.php', $this->rootPath, $controller, $viewName );		// php
			if ( file_exists( $view))
				return ( $view);

			/*-- ---- --*/

			$altView = sprintf( '%s/views/%s/%s.md', $this->rootPath, $controller, $viewName);	// markdown

			if ( file_exists( $altView))
				return ( $altView);

		}


		/* there is nothing in the [application]/views/[controller]/ folder
			=> look in [app]/views/[controller]/ folder */

		if ( preg_match( '/\.(php|md)$/', $viewName)) {		// extension was specified
			$view = sprintf( '%s/app/views/%s/%s', $this->rootPath, $controller, $viewName );
			if ( file_exists( $view))
				return ( $view);

		}
		else {
			$view = sprintf( '%s/app/views/%s/%s.php', $this->rootPath, $controller, $viewName );		// php
			if ( file_exists( $view))
				return ( $view);

			/*-- ---- --*/

			$altView = sprintf( '%s/app/views/%s/%s.md', $this->rootPath, $controller, $viewName);	// markdown

			if ( file_exists( $altView))
				return ( $altView);

		}
		/*-- ---- --*/

		$commonPath = strings::getCommonPath( array( __DIR__, $this->rootPath));

		/* there is nothing in the [application]/app/views/[controller]/ folder
			=> look in [app]/views/ folder */

		if ( preg_match( '/\.(php|md)$/', $viewName)) {		// extension was specified
			$altView = sprintf( '%s/app/views/%s', $this->rootPath, $viewName );
			if ( $this->debug) \sys::logger( '_controller->getView :: check local view : ' .
				preg_replace( '@^' . $commonPath . '@', '', $altView));

			if ( file_exists( $altView))
				return ( $altView);

		}
		else {
			$altView = sprintf( '%s/app/views/%s.php', $this->rootPath, $viewName );	// php
			if ( $this->debug) \sys::logger( '_controller->getView :: check local view : ' .
				preg_replace( '@^' . $commonPath . '@', '', $altView));

			if ( file_exists( $altView))
				return ( $altView);

			/*-- ---- --*/

			$altView = sprintf( '%s/app/views/%s.md', $this->rootPath, $viewName );	// markdown
			if ( $this->debug) \sys::logger( '_controller->getView :: check for local markdown : ' .
				preg_replace( '@^' . $commonPath . '@', '', $altView));

			if ( file_exists( $altView))
				return ( $altView);

		}

		/* there is nothing in then [application]

			first look for a php view, then a markdown

			look to tye [system] folders
				[system]/views/[controller]
				[system]/app/views/	*/

		/*-- ---- [system]/views/[controller] folder ---- --*/
		if ( preg_match( '/\.(php|md)$/', $viewName)) {		// extension was specified
			$altView = sprintf( '%s/views/%s/%s', __DIR__, $controller, $viewName );
			if ( $this->debug) \sys::logger( '_controller->getView :: check system view : ' .
				preg_replace( '@^' . $commonPath . '@', '', $altView));

			if ( file_exists( $altView))
				return ( $altView);

		}
		else {
			$altView = sprintf( '%s/views/%s/%s.php', __DIR__, $controller, $viewName );	// php
			if ( $this->debug) \sys::logger( '_controller->getView :: check system view : ' .
				preg_replace( '@^' . $commonPath . '@', '', $altView));

			if ( file_exists( $altView))
				return ( $altView);

			$altView = sprintf( '%s/views/%s/%s.md', __DIR__, $controller, $viewName );	// md
			if ( $this->debug) \sys::logger( '_controller->getView :: check system view : ' .
				preg_replace( '@^' . $commonPath . '@', '', $altView));

			if ( file_exists( $altView))
				return ( $altView);

		}

		/*-- ---- [system]/views/ folder ---- --*/
		if ( preg_match( '/\.(php|md)$/', $viewName)) {		// extension was specified
			$altView = sprintf( '%s/views/%s', __DIR__, $viewName );	// php
			if ( $this->debug) \sys::logger( '_controller->getView :: check local default view : ' .
				preg_replace( '@^' . $commonPath . '@', '', $altView));

			if ( file_exists( $altView))
				return ( $altView);

		}
		else {
			$altView = sprintf( '%s/views/%s.php', __DIR__, $viewName );	// php
			if ( $this->debug) \sys::logger( '_controller->getView :: check local default view : ' .
				preg_replace( '@^' . $commonPath . '@', '', $altView));

			if ( file_exists( $altView))
				return ( $altView);

			$altView = sprintf( '%s/views/%s.md', __DIR__, $viewName );	// md
			if ( $this->debug) \sys::logger( '_controller->getView :: check local default view : ' .
				preg_replace( '@^' . $commonPath . '@', '', $altView));

			if ( file_exists( $altView))
				return ( $altView);

		}
		/*-- ---- --*/

		if ( $this->debug) \sys::logger( '_controller->getView :: no view found');

		return __DIR__ . '/views/not-found.md';

	}

	protected function loadView( $name, $controller = NULL ) {
		return ( $this->load( $name, $controller));

	}

	protected function load( $viewName = 'index', $controller = NULL ) {
		$view = $this->getView( $viewName, $controller );
		if ( substr_compare( $view, '.md', -3) === 0) {
			if ( $this->debug) sys::logger( '_controller->loadView :: it\'s an md !');
			$fc = file_get_contents( $view);

			print \Parsedown::instance()->text( $fc);

		}
		else {
			require ( $view);

		}

	}

	public function logout() {
		\session::destroy();
		\Response::redirect( \url::$URL );

	}

	public function logoff() {
		$this->logout();

	}

	public function init( $name = '') {
		self::$url = sprintf( '%s%s/', \url::$URL, $name );
		\sys::logger( self::$url, 5);

	}

	public function errorTest() {
		throw new \dvc\Exceptions\GeneralException;

	}

}
