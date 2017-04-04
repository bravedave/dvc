<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/
NameSpace dvc;

class view {
	var $data = NULL,
		$rootPath = NULL,
		$loadName = '?',
		$wrap = array();

	function __construct( $data = NULL ) {
		if ( $app = application::app())
			$this->rootPath = $app->getRootPath();

		else
			$this->rootPath = __DIR__;


		$this->data = $data;

	}

	function loadView( $name ) {
		return ( $this->load( $name ));

	}

	function load( $name ) {
		if ( preg_match( sprintf( '@^%s@', $this->rootPath), $name )) {
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
			if ( count( (array)$this->wrap)) {
				foreach( (array)$this->wrap as $wrap) {
					if ( $wrap)
						printf( '<div class="%s">', $wrap);

				}

			}

			include $path;

			if ( count( (array)$this->wrap)) {
				foreach( (array)$this->wrap as $wrap) {
					if ( $wrap)
						printf( '</div><!-- wrap:div class="%s" -->', $wrap);

				}

			}

			return ( TRUE);

		}
		else {
			//~ sys::logger( sprintf( 'not found::%s', $path ));

			$path = sprintf( '%s/views/%s.php', __DIR__, $name );
			if ( file_exists( $path)) {
				if ( count( (array)$this->wrap)) {
					foreach( (array)$this->wrap as $wrap) {
						if ( $wrap)
							printf( '<div class="%s">', $wrap);

					}

				}

				include $path;

				if ( count( (array)$this->wrap)) {
					foreach( (array)$this->wrap as $wrap) {
						if ( $wrap)
							printf( '</div><!-- wrap:div class="%s" -->', $wrap);

					}

				}

				return ( TRUE);

			}
			else {
				printf( 'view::%s - not found', $name );


			}

		}

		return ( FALSE);

	}

}