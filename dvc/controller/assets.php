<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/

class assets extends Controller {
	public $RequireValidation = FALSE;

	protected function _index() {}

	public function bootstrap( $type = 'css', $version = 4) {
		if ( 4 == (int)$version) {
			sys::serveBootStrap( $type);

		}

	}

	public function jquery() {
		\sys::serve( sprintf('%s/../js/%s', __DIR__, 'jquery-3.3.1.min.js'));

	}

	public function brayworth( $type = 'css') {
		if ( 'css' == $type) {

		}
		else {
			// sys::dump( \jslib::$brayworthlibFiles);
			$files = [];
			foreach( \jslib::$brayworthlibFiles as $f) {
				$path = sprintf( '%s/../%s', __DIR__, $f);
				// printf( '%s<br />', $path);
				if ( $_f = realpath( $path)) {
					$key = basename( $_f);
					$files[ $key] = $_f;

				}

			}

			if ( $type == 'bundle') {
				array_unshift( $files, sprintf('%s/../js/%s', __DIR__, 'jquery-3.3.1.min.js'));

			}

			// sys::dump( $files);

			jslib::viewjs([
				'debug' => FALSE,
				'libName' => 'brayworth',
				'jsFiles' => $files,
				'libFile' => config::tempdir()  . '_brayworth_tmp.js'

			]);

		}

	}

}
