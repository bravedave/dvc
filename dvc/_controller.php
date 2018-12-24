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
namespace dvc;
use dao;

abstract class _controller {
	public $authorized = false;
	public $authorised = false;
	public $RequireValidation = true;
	public $CheckOffline = true;
	public $db = null;
	public $name = 'home';
	public $Request = null;
	public $timer = null;
	public $rootPath  = '';
	public $defaultController = 'home';
	public $title;
	public $debug = false;

	protected $Redirect_OnLogon = false;
	protected $label = null;

	static $url;

	const viewNotFound = __DIR__ . '/views/not-found.md';

	function __construct( $rootPath ) {
		if ( $this->debug) \sys::logger( __FILE__ . ' :: start construct');
		$this->rootPath = $rootPath;
		$this->title = config::$WEBNAME;
		if ( is_null( $this->label)) {
			$this->label = ucwords( get_class( $this));

		}

		/**
		 * When a controller is created, open a database connection.
		 * There is ONE connection that is used globally.
		 */
		$this->db = application::app()->dbi();
		$this->Request = application::Request();

		if ( $this->debug) \sys::logger( __FILE__ . ' :: checking authority');
		$this->authorised = \currentUser::valid();
		if ( $this->debug) \sys::logger( __FILE__ . ' :: checking authority :: ' . ( $this->authorised ? 'private' : 'public'));

		if ( $this->RequireValidation ) {
			if ( !( $this->authorised)) {
				$this->authorize();

			}

		}
		elseif ( $this->isPost()) {
			/**
			 * possibly authentication is not turned
			 * on but they have a page that requires
			 * validation
			 */
			$action = $this->getPost( 'action');
			if ( $action == '-system-logon-') {
				if ( !( $this->authorised))
					$this->authorize();

			}

		}

		if ( $this->CheckOffline ) {
			if ( $this->authorised) {
				if ( !\currentUser::isadmin()) {
					$state = new dao\state;
					if ( $state->offline())
						Response::redirect( \url::tostring( 'offline'));

				}

			}

		}

		$this->authorized = $this->authorised;	// american spelling accepted (doh)

		if ( $this->RequireValidation) {
			if ( $this->access_control()) {
				$this->before();

			}
			else {
				/* The user is $authorised, but denied access by their _acl	*/
				Response::redirect( url::tostring());

			}

		}
		else {
			// no acl required for anon access
			$this->before();

		}

	}

	protected function access_control() {
		// warning - don't impose an access_control of FALSE on the home page !
		return ( TRUE);

	}

	protected function before() {
		/*
		* Placeholder for use by the child class.
		* This method is called
		* at the end of __construct()
		*
		* avoid replacing the default __construct method - use before instead
		*
		* Inspired by something I read in the fuelPHP documentation
		* this method is called at the end of __construct and can
		* be used to modify the _controller class
		*/

	}

	protected function getParam( $v = '', $default = false ) {
		if ( is_null( $this->Request ))
			return ( FALSE);

		return ( $this->Request->getParam( $v, $default));

	}

	protected function isPost() {
		if ( is_null( $this->Request ))
			return ( FALSE);

		return ( $this->Request->isPost());

	}

	protected function getPost( $name = '', $default = false ) {
		if ( is_null( $this->Request ))
			return ( false);

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

		if ( \config::use_inline_logon) {
			$p = new \config::$PAGE_TEMPLATE_LOGON( 'Log On' );
				$p->footer = FALSE;
				if ( \config::allow_password_recovery) {
					$p->latescripts[] = '<script>$(document).ready( function() { _brayworth_.logon_retrieve_password = true; _brayworth_.logonModal(); })</script>';

				}
				else {
					$p->latescripts[] = '<script>$(document).ready( function() { _brayworth_.logonModal(); })</script>';

				}

				$p->meta[] = '<meta name="viewport" content="initial-scale=1" />';
				$p
					->header()
					->content();
				//~ $this->loadView( 'logon');

		}
		else {
			if ( $this->Redirect_OnLogon)
				Response::redirect( \url::tostring( sprintf( 'logon?referer=%s', $this->Redirect_OnLogon)));

			else
				Response::redirect( \url::tostring( 'logon'));

		}
		die;

	}

	protected function dbResult( $query) {
		/*
		* Return a SQL Data Result using
		* the default data adapter
		*/
		if ( is_null( $this->db )) {
			return ( false);

		}

		return ( $this->db->Result( $query));

	}

	protected function SQL( $query) {
		/*
		* Perform an SQL Command using
		* the default data adapter
		*/
		if ( is_null( $this->db )) {
			return ( FALSE);

		}

		return ( $this->db->SQL( $query));

	}

	protected function dbEscape( $s) {
		/*
		* Escape a string for inclusing in an SQL
		* Command using the default data adapter
		*/
		if ( is_null( $this->db )) {
			return ( $s);

		}

		return ( $this->db->escape( $s));

	}

	public function index() {
		$this->page404();

	}

	public function page404() {
		header('HTTP/1.0 404 Not Found');
		$this->render([
			'title' => '404 Not Found',
			'content' => 'not-found'

		]);

	}

	protected function hasView( $viewName = 'index', $controller = NULL ) {
		return $this->getView( $viewName, $controller) != self::viewNotFound;
		//~ $view = $this->getView( $viewName, $controller);
		//~ return ( file_exists( $view));

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
		*
		*			first look for a php view, then a markdown
		*
		*			look to the [theme] folder
		*				[theme]/views/
		*/
		if ( class_exists( 'dvc\theme\view', /* autoload */ false)) {
			if ( $altView = theme\view::getView( $viewName)) {
				return ( $altView);

			}

		}

		/* there is nothing in then [application] || [theme]
		*
		*			first look for a php view, then a markdown
		*
		*			look to the [system] folders
		*				[system]/views/[controller]
		*				[system]/app/views/
		*/

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
			if ( $this->debug) {
				\sys::logger(
					sprintf( '_controller->getView :: check local default view : %s',
					preg_replace( '@^' . $commonPath . '@', '', $altView))

				);

			}

			if ( file_exists( $altView)) {
				return ( $altView);

			}

			$altView = sprintf( '%s/views/%s.md', __DIR__, $viewName );	// md
			if ( $this->debug) {
				\sys::logger(
					sprintf( '_controller->getView :: check local default view : %s',
					preg_replace( '@^' . $commonPath . '@', '', $altView))

				);

			}

			if ( file_exists( $altView)) {
				return ( $altView);

			}

		}
		/*-- ---- --*/

		// \sys::logger( sprintf( '_controller->getView :: view not found : %s', $viewName));
		\sys::trace( sprintf( '_controller->getView :: view not found : %s', $viewName));
		return self::viewNotFound;

	}

	protected function loadView( $name, $controller = NULL ) {
		return ( $this->load( $name, $controller));

	}

	protected function load( $viewName = 'index', $controller = null ) {
		$view = $this->getView( $viewName, $controller );
		if ( substr_compare( $view, '.md', -3) === 0) {
			if ( $this->debug) sys::logger( '_controller->loadView :: it\'s an md !');
			$fc = file_get_contents( $view);

			printf(
				'<div class="markdown-body">%s</div>',
				\Parsedown::instance()->text( $fc)

			);

		}
		else {
			require ( $view);

		}

		return ( $this);

	}

	protected function page( $params) {
		$defaults = [
			'css' => [],
			'data' => false,
			'footer' => true,
			'latescripts' => [],
			'meta' => [],
			'scripts' => [],
			'title' => $this->title,
			'bodyClass' => false,
			'template' => \config::$PAGE_TEMPLATE,

		];

		$options = array_merge( $defaults, $params);

		$p = new $options['template']( $options['title']);
		$p->bodyClass = $options['bodyClass'];
		$p->footer = $options['footer'];
		$p->data = (object)$options['data'];
		if ( !( isset( $p->data->title))) {
			$p->data->title = $options['title'];

		}

		foreach ( $options['css'] as $css) {
			if ( preg_match('/^<link/', $css)) {
				$p->css[] = $css;

			}
			else {
				$p->css[] = sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', $css);

			}

		}

		foreach ( $options['meta'] as $meta) {
			$p->meta[] = $meta;

		}

		foreach ( $options['scripts'] as $script) {
			if ( preg_match('/^<script/', $script)) {
				$p->scripts[] = $script;

			}
			else {
				$p->scripts[] = sprintf( '<script type="text/javascript" src="%s"></script>', $script );

			}

		}

		/*
		* latescripts are prepended
		* - if something like tinymce is appended after it would be slower
		*/
		foreach ( $options['latescripts'] as $script) {
			if ( preg_match('/^<script/', $script)) {
				array_unshift( $p->latescripts, $script);

			}
			else {
				array_unshift( $p->latescripts, sprintf( '<script type="text/javascript" src="%s"></script>', $script));

			}

		}

		return ( $p);

	}

	protected function modal( $params = []) {
		$defaults = [
			'title' => sprintf( '%s Modal', \config::$WEBNAME),
			'class' => '',
			'header-class' => 'text-white bg-secondary py-2',
			'load' => false,
			'text' => false,
		];

		$options = array_merge( $defaults, $params);

		\Response::html_headers();
		$m = new pages\modal([
			'title' => $options['title'],
			'class' => $options['class'],
			'header-class' => $options['header-class'],
		]);

		$m->open();

		if ( $options['load']) {
			foreach ( (array)$options['load'] as $_) {
				$this->load( $_);

			}

		}

		if ( $options['text']) {
			foreach ( (array)$options['text'] as $_) {
				print $_;

			}

		}

	}

	protected function _render( $view) {
		foreach( (array)$view as $_){
			$this->load( $_);

		}

	}

	protected function render( $params) {
		$defaults = [
			'left-interface' => false,
			'main' => false,
			'main-panel' => false,
			'primary' => false,
			'secondary' => false,
			'sidebar' => false,
			'content' => false,
			'navbar' => '',

		];

		$options = array_merge( $defaults, $params);

		$p = $this->page( $options);

		$p	->header()
  			->title( $options['navbar']);

		if ( $options['left-interface']) {
			if ( $options['secondary']) {
				$p->secondary();
				$this->_render( $options['secondary']);

			}

			if ( $options['primary']) {
				$p->primary();
				$this->_render( $options['primary']);

			}

		}
		else {
			if ( $options['primary']) {
				$p->primary();
				$this->_render( $options['primary']);

			}

			if ( $options['secondary']) {
				$p->secondary();
				$this->_render( $options['secondary']);

			}

		}

		if ( $options['content']) {
			$p->content();
			$this->_render( $options['content']);

		}

		if ( $options['sidebar']) {
			/*
			Tip 1: You can change the color of the sidebar using: data-color="purple | blue | green | orange | red"
			Tip 2: you can also add an image using data-image tag
			*/
			$more = null;
			if ( 'lightdash' == \config::$THEME) {
				$more = sprintf('data-color="orange" data-image="%s"', \url::tostring('theme/img/sidebar-5.jpg'));

			}
			elseif ( 'material-dashboard' == \config::$THEME) {
				$more = sprintf('data-color="rose" data-background-color="black" data-image="%s"', \url::tostring('theme/img/sidebar-1.jpg'));

			}

			$p->newSection( $name = 'sidebar', $class = 'sidebar', $role = 'sidebar', $more = '');
			$this->_render( $options['sidebar']);

		}

		if ( $options['main-panel']) {
			$p->newSection( $name = 'main-panel', $class = 'main-panel', $role = 'main-panel', $more = '');
			$this->_render( $options['main-panel']);

		}

		if ( $options['main']) {
			$p->newSection( $name = 'main', $class = 'main', $role = 'main', $more = '');
			$this->_render( $options['main']);

		}
		
		return ( $p);

	}

	public function logout() {
		\session::destroy();
		\Response::redirect( \url::$URL );
		header('HTTP/1.1 401 Unauthorized');

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
