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

	/**
	 * Whenever a controller is created, open a database connection too.
	 * The idea behind is to have ONE connection that can be used by multiple models.
	 */
	function __construct( $rootPath ) {
		if ( $this->debug) \sys::logger( __FILE__ . ' :: start construct');
		$this->rootPath = $rootPath;
		$this->title = config::$WEBNAME;
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
						Response::redirect( \url::$URL . "offline" );

				}

			}

		}

		$this->authorized = $this->authorised;	// american spelling accepted (doh)

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
				Response::redirect( \url::$URL . 'auth/request' );
				return;

			}

		}

		if ( $this->Redirect_OnLogon)
			Response::redirect( sprintf( '%slogon?referer=%s', \url::$URL, $this->Redirect_OnLogon));

		else
			Response::redirect( \url::$URL . 'logon' );

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
		if ( $this->hasView())
			$this->loadView();

		else
			$this->page404();

	}

	public function page404() {
		header('HTTP/1.0 404 Not Found');
		$p = new Page('404 Not Found');
			$p->header();
			$p->title();

			$p->content();
			$this->loadView('404');

	}

	protected function hasView( $viewName = 'index', $controller = NULL ) {
		$view = $this->getView( $viewName, $controller);
		return ( file_exists( $view));

	}

	protected function getView( $viewName = 'index', $controller = NULL ) {
		if ( is_null( $controller ))
			$controller = $this->name;

		$view = sprintf( '%s/views/%s/%s.php', $this->rootPath, $controller, $viewName );
		$commonPath = strings::getCommonPath( array( __DIR__, $this->rootPath));

		if ( !file_exists( $view)) {
			$altView = sprintf( '%s/views/%s/%s.php', __DIR__, $controller, $viewName );	// is there a default viewer
			if ( $this->debug) \sys::logger( '_controller->getView :: ' . $commonPath);
			if ( $this->debug) \sys::logger( '_controller->getView :: check system view : ' . preg_replace( '@^' . $commonPath . '@', '', $altView));
			if ( file_exists( $altView)) {
				$view = $altView;

			}
			else {
				$altView = sprintf( '%s/app/views/%s.php', $this->rootPath, $viewName );
				if ( $this->debug) \sys::logger( '_controller->getView :: check local view : ' . preg_replace( '@^' . $commonPath . '@', '', $altView));
				if ( file_exists( $altView)) {
					$view = $altView;

				}
				else {
					$altView = sprintf( '%s/views/%s.php', __DIR__, $viewName );
					if ( $this->debug) \sys::logger( '_controller->getView :: check local default view : ' . preg_replace( '@^' . $commonPath . '@', '', $altView));
					if ( file_exists( $altView)) {
						$view = $altView;

					}
					else {
						$altView = sprintf( '%s/views/%s/%s.md', $this->rootPath, $controller, $viewName );
						if ( $this->debug) \sys::logger( '_controller->getView :: check for markdown : ' . preg_replace( '@^' . $commonPath . '@', '', $altView));
						if ( file_exists( $altView)) {
							$view = $altView;

						}
						else {
							if ( $this->debug) \sys::logger( '_controller->getView :: no view found');

						}

					}

				}

			}

		}

		if ( config::$CREATE_CONTROLLER_SYMLINKS) {
			/*****************************************
			 * This is a pure programming thing
			 *	- if the view folder exists, and there is not a link to the controller, create one
			 *
			 *	just makes it easier to open the controller when you are programming
			 *
			 *	to implement this the views/folders need to be writable to the server
			 *
			 **/
			if ( !( preg_match( '@$win@i', PHP_OS ))) {	// not on windows
				$_controller = sprintf( '%s/views/%s/_%s.php', $this->rootPath, $this->name, $this->name );
				if ( !file_exists( $_controller)) {
					$viewDir = sprintf( '%s/views/%s/', $this->rootPath, $this->name );
					if ( file_exists($viewDir) && is_writable( $viewDir)) {
						$_self = sprintf( '%s/controller/%s.php', $this->rootPath, $this->name );
						$src = explode( '/', $_self );
						$tgt = explode( '/', $_controller );
						while ( count( $src) && count( $tgt)) {
							if ( $src[0] != $tgt[0])
								break;

							array_shift( $src);
							array_shift( $tgt);

						}

						if ( count( $src)) {
							array_unshift( $src, '..' );
							array_unshift( $src, '..' );
							$_self = implode( '/', $src );

							sys::logger( sprintf( 'controller link does not exist - create : %s => %s', $_controller, $_self));
							symlink( $_self, $_controller);

						}

					}
					else {
						if ( file_exists($viewDir)) {
							sys::logger( sprintf( 'controller link does not exist but %s is not writable', $viewDir));

						}

					}

				}

			}

		}
		elseif ( config::$REMOVE_CONTROLLER_SYMLINKS) {
			/*****************************************
			 * This is a pure programming thing
			 *	- if the view folder exists, and there is not a link to the controller, create one
			 *
			 *	just makes it easier to open the controller when you are programming
			 *
			 *	to implement this the views/folders need to be writable to the server
			 *
			 **/
			if ( !( preg_match( '@$win@i', PHP_OS ))) {	// not on windows
				$_controller = sprintf( '%s/views/%s/_%s.php', $this->rootPath, $this->name, $this->name );
				if ( file_exists( $_controller) && is_link( $_controller)) {
					$viewDir = sprintf( '%s/views/%s/', $this->rootPath, $this->name );
					if ( file_exists($viewDir) && is_writable( $viewDir)) {
						unlink( $_controller);
						sys::logger( 'removed controller link -  ' . $_controller);

					}

				}

			}

		}

		return $view;

	}

	protected function loadView( $viewName = 'index', $controller = NULL ) {
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

	protected function init( $name = '') {
		self::$url = sprintf( '%s%s/', \url::$URL, $name );
		\sys::logger( self::$url, 5);

	}

	public function errorTest() {
		throw new \dvc\Exceptions\GeneralException;

	}

}
