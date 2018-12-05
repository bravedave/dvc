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
	public $debug = false;

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
				if ( $wrap)
					printf( '<div class="%s">', $wrap);

			}

		}

	}

	protected function _unwrap() {
		if ( count( (array)$this->wrap)) {
			foreach( (array)$this->wrap as $wrap) {
				if ( $wrap)
					printf( '</div><!-- wrap:div class="%s" -->', $wrap);

			}

		}

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
			$this->_wrap();
			$this->_load( $path);
			$this->_unwrap();

			return ( true);

		}
		else {
			//~ sys::logger( sprintf( 'not found::%s', $path ));

			$path = sprintf( '%s/views/%s.php', __DIR__, $name );
			if ( file_exists( $path)) {
				$this->_wrap();
				$this->_load( $path);
				$this->_unwrap();

				return ( true);

			}
			else {
				printf( 'view::%s - not found', $name );

			}

		}

		return ( false);

	}

}