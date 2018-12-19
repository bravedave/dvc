<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/
namespace dvc;

class view {
	public $data = null;
	public $rootPath = null;
	public $loadName = '?';
	public $title = '';
	public $wrap = [];
	public $debug = true;

	function __construct( $data = null) {
		if ( $app = application::app())
			$this->rootPath = $app->getRootPath();

		else
			$this->rootPath = __DIR__;


		$this->data = $data;

	}

	static function instance() {
		return new self;

	}

	protected function _wrap() {
		if ( count( (array)$this->wrap)) {
			foreach( (array)$this->wrap as $wrap) {
				if ( $wrap) {
					printf( '<div class="%s">', $wrap);

				}

			}

		}

		return ( $this);	// chain

	}

	protected function _unwrap() {
		if ( count( (array)$this->wrap)) {
			foreach( (array)$this->wrap as $wrap) {
				if ( $wrap)
					printf( '</div><!-- wrap:div class="%s" -->', $wrap);

			}

		}

		return ( $this);	// chain

	}

	protected function _load( $path) {
		if ( substr_compare( $path, '.md', -3) === 0) {
			if ( $this->debug) \sys::logger( 'dvc\view->_load :: it\'s an md !');
			$fc = file_get_contents( $path);

			print \Parsedown::instance()->text( $fc);

		}
		else {
			include $path;

		}

		return ( $this);	// chain

	}

	function loadView( $name) {
		return ( $this->load( $name ));

	}

	function load( $name) {
		if ( substr($name, 0, strlen($this->rootPath)) === $this->rootPath) {
			if ( file_exists( $name)) {
				$parts = pathinfo( $name);
				$this->loadName = $parts['filename'];
				$path = $name;
				//~ \sys::logger( sprintf( 'dvc\view->load :: rootpath in name %s', $this->loadName));

			}

		}
		else {
			$this->loadName = $name;
			$path = sprintf( '%s/app/views/%s.php', $this->rootPath, $name );

		}

		if ( file_exists( $path)) {
			$this
				->_wrap()
				->_load( $path)
				->_unwrap();

			return ( true);

		}
		else {
			//~ sys::logger( sprintf( 'not found::%s', $path ));

			$path = sprintf( '%s/views/%s.php', __DIR__, $name );
			if ( file_exists( $path)) {
				$this
					->_wrap()
					->_load( $path)
					->_unwrap();

				return ( true);

			}
			else {
				// we are going to allow vendor/bravedave/dvc/theme
				if ( class_exists( 'dvc\theme\view', /* autoload */ false)) {
					if ( $themeView = theme\view::getView( $name)) {
						$this
							->_wrap()
							->_load( $themeView)
							->_unwrap();

						return ( true);

					}

				}

				printf( 'view::%s - not found<br />', $name );
				printf( 'root::%s<br />', $this->rootPath );
				print '<br />';

			}

		}

		return ( false);

	}

}
