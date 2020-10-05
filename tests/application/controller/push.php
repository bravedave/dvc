<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * styleguide : https://codeguide.co/
*/

class push extends Controller {
	protected function _index() {
    $render = [
      'footer' => implode( DIRECTORY_SEPARATOR, [
        application::app()->getRootPath(),
        'app',
        'views',
        'push',
        'footer'

      ]),
      'title' => 'Push Notifications',
      'primary' => 'about',
      'secondary' => [
        'index-hello',
        'index'

      ]

    ];

    if ( \config::$SYNTAX_HIGHLIGHT_DOCS) {
			// '<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/10.1.1/styles/default.min.css">'
			$render['css'] = [
				'<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/10.1.1/styles/github-gist.min.css">'

			];

			$render['scripts'] = [
				'<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/10.1.1/highlight.min.js"></script>',
				'<script>hljs.initHighlightingOnLoad();</script>'

			];

		}

		$this->render( $render);

  }

}
