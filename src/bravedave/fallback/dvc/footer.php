<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 *  DO NOT change this file
 *  Copy it to <application>/app/dvc/ and modify it there
*/

namespace dvc;

class footer extends html\footer {
	protected $ul = null;

	function __construct() {
		$content = new html\div( null, array( 'class' => 'container'));
			$nav = $content->append( 'nav', null, [ 'class' => 'pull-left' ]);
			$this->ul = $nav->append( 'ul');

			$div = $content->append( 'div', null, [ 'class' => 'pull-right' ]);
				$ul = $div->append( 'ul', null, [ 'class' => 'copyright']);
					$ul->add( sprintf( '&copy; Brayworth %s <i class="bi bi-code-slash"></i>', date('Y')));

		// sys::logger('footer extends html\footer - parse to parent');
		parent::__construct( $content);

	}

	public function panelItem( html\element $element ) {
		$this->ul->append( 'li', $element);

	}

}
