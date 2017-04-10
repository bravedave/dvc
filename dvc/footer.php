<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/
Namespace dvc;

class footer extends html\footer {
		protected $ul = NULL;

		function __construct() {
				$content = new html\div( NULL, array( 'class' => 'container'));
						$nav = $content->append( 'nav', NULL, array( 'class' => 'pull-left' ));
						$this->ul = $nav->append( 'ul');

						$div = $content->append( 'div', NULL, array( 'class' => 'pull-right' ));
								$ul = $div->append( 'ul', NULL, array( 'class' => 'copyright'));
									$ul->add( sprintf( '&copy; Brayworth %s <i class="fa fa-code"></i>', date('Y')));

				sys::logger('footer extends html\footer - parse to parent');
				parent::__construct( $content);

		}

		public function panelItem( html\element $element ) {
				$this->ul->append( 'li', $element);

		}

}
