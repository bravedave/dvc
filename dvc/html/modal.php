<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/

NameSpace dvc\html;

class modal extends div {
	function __construct( $id = 'myModal' ) {
		parent::__construct();

		$this->attributes( array(
			'class' => 'modal fade',
			'id' => (string)$id,
			'tabindex' => '-1',
			'role' => 'dialog',
			'aria-labelledby' => 'myModalLabel',
			'aria-hidden' => 'true' ));

		$div = $this->append( 'div', NULL, array(
			'class' => 'modal-dialog',
			'id' => (string)$id . 'Dialog' ));

			$content = $div->append( 'div', NULL, array(
				'class' => 'modal-content' ));

				$header = $content->append( 'div', NULL, array(
					'class' => 'modal-header' ));

					$button = $header->append( 'button', NULL, array(
						'type' => 'button',
						'class' => 'close',
						'data-dismiss' => 'modal',
						'aria-label' => 'Close' ));

						$button->append( 'span', '&times;', array( 'aria-hidden' => 'true' ));

					$header->append( 'h4', 'Modal title', array(
						'class' => 'modal-title',
						'id' => (string)$id . 'Title' ));

				$content->append( 'div', 'Modal Body', array(
					'class' => 'modal-body',
					'id' => (string)$id . 'Body' ));

				$content->append( 'div', '&nbsp;', array(
					'class' => 'modal-footer',
					'id' => (string)$id . 'Footer' ));

	}

}
