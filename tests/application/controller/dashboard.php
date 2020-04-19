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
				'title' => $this->title = $this->label,
				'sidebar' => 'sidebar',
				'main' => 'main',
				'data' => (object)[
					'searchFocus' => true,
					'pageUrl' => strings::url( $this->route)

				],

			]

		);

	}

	protected function changelog() {
		$path = realpath( implode( DIRECTORY_SEPARATOR, [
			__DIR__,
			'..',
			'..',
			'..',
			'CHANGELOG.md'
			])

		);

		if ( file_exists( $path)) {
			$fc = file_get_contents( $path);
			print \Parsedown::instance()->text( $fc);

		}

	}

    public function doc( $index = '') {
		if ( $index) {
			if ( 'changes' == $index) {
				$this->changelog();

			}
			else {
				if ( file_exists( __DIR__ . '/../app/views/dashboard/' . $index  . '.md')) {
					$this->load( $index);

				}

			}

		}

	}

    public function css() {
        Response::css_headers();
        sys::serve( __DIR__ . '/../app/views/dashboard/custom.css');

    }

}
