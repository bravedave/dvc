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
      $root = realpath( sprintf( '%s/../../../../twbs', __DIR__));
      if ( $root) {
        $path = realpath( sprintf( '%s/bootstrap/dist', $root));
        // printf( '%s<br />', $path);
        if ( 'css' == $type) {
          $lib = sprintf( '%s/css/bootstrap.min.css',$path);
          \sys::serve( $lib);
          // printf( '%s<br />', $lib);

        }
        elseif ( 'js' == $type) {
          $lib = sprintf( '%s/js/bootstrap.bundle.min.js',$path);
          \sys::serve( $lib);
          // printf( '%s<br />', $lib);

        }

      }
      else {
        throw new \Exception( 'Cannot locate twbs bootstrap - install with compose require twbs/bootstrap');

      }

    }

  }

  public function jquery() {
    // $root = realpath( sprintf( '%s/../../../../components/jquery', __DIR__));
    // if ( $root) {
    //   $path = realpath( sprintf( '%s/jquery.min.js', $root));
    //   \sys::serve( $path);
    //
    // }
    // else {
    //   throw new \Exception( 'Cannot locate twbs bootstrap - install with compose require twbs/bootstrap');
    //
    // }
    \sys::serve( sprintf('%s/../%s', __DIR__, 'jquery-3.3.1.min.js'));

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
