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

	protected function _index( $view = 'index') {
		if ( !$view) {
			$view = 'index';

		}
		//~ $this->debug = true;

		$contents = ['contents','work'];
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

    $primary = [(string)$view];
    if ( 'icons' == $view) {
      $primary[] = 'icons-code';
      $primary[] = 'icons-credit';

    }
    $primary[] = 'docs-format';

		$render = [
			'title' => $this->title = sprintf( 'Docs - %s', ucwords( $view)),
			'primary' => $primary,
			'secondary' => $contents,

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

	public function index( $view = 'index') {
		$this->isPost() ?
			$this->postHandler() :
			$this->_index( $view);

	}

}
