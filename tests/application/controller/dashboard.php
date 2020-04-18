<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

class dashboard extends Controller {
	protected function posthandler() {
		$action = $this->getPost('action');

		if ( 'gibblegok' == $action) {
			\Json::ack( $action);

		}
		else {
			parent::postHandler();

		}

	}

	protected function render( $params) {
        $defaults = [
            'navbar' => 'dashboard/navbar',
            'css' => [
                'dashboard/css'
            ],
            'template' => 'dvc\pages\reboot',

		];

        $options = array_merge( $defaults, $params);

        return parent::render( $options);

    }

	protected function _index() {
		$this->render(
			[
				'title' => $this->title = sprintf( '%s : Index', $this->label),
				'sidebar' => 'sidebar',
				'main' => 'main',
				'data' => (object)[
					'searchFocus' => true,
					'pageUrl' => strings::url( $this->route)

				],

			]

		);

    }

    public function css() {
        Response::css_headers();
        sys::serve( __DIR__ . '/../app/views/dashboard/custom.css');

    }

}
