<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

class docs extends Controller {
	public $RequireValidation = \config::lockdown;

	protected function render( $params) {
        $defaults = [
            'navbar' => 'navbar',
            'css' => [
                strings::url( 'docs/css')
            ],
			'template' => 'dvc\pages\reboot',

		];

        $options = array_merge( $defaults, $params);

        return parent::render( $options);

    }

    public function css() {
        Response::css_headers();
        sys::serve( __DIR__ . '/../views/docs/custom.css');

    }

	public function index( $view = 'index') {
		if ( !$view) {
			$view = 'index';

		}
		//~ $this->debug = true;

		$contents = 'contents';
		if ( $this->hasView( $_c = sprintf( '%s-contents', $view))) {
			$contents = $_c;

		}
		else if ( strpos( $view, '-') !== false) {
			//~ die( $view);
			//~ die( sprintf( '%s-contents', preg_replace( '/-.*/', '', $view)));

			if ( $this->hasView( $_c = sprintf( '%s-contents', preg_replace( '/-.*/', '', $view)))) {
				$contents = $_c;

			}

		}

		$this->render([
			'title' => $this->title = sprintf( 'Docs - %s', ucwords( $view)),
			'primary' => [(string)$view, 'docs-format'],
			'secondary' => $contents,

		]);

	}

}
