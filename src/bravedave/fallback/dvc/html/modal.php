<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

nameSpace dvc\html;

use dvc\bs;
use strings;

class modal extends div {
	protected $_body;

	protected $_dialog;

	protected $_footer;

	protected $_header;

	protected $_title;

	protected static $FORM_WRAPPER = false;

	public function __construct( $id = '' ) {
		parent::__construct();

		if ( !$id) $id = strings::rand();

		$this->attributes( [
			'class' => 'modal fade',
			'id' => (string)$id,
			'tabindex' => '-1',
			'role' => 'dialog',
			'aria-labelledby' => (string)$id . 'Label',
			'aria-hidden' => 'true'

		]);

		$this->_dialog = $this->append( 'div', null, [
			'class' => 'modal-dialog modal-dialog-centered',
			'id' => (string)$id . 'Dialog'

    ]);

    $content = $this->_dialog->append( 'div', null, [
      'class' => 'modal-content'

    ]);

    $this->_header = $content->append( 'div', null, [
      'class' => 'modal-header py-2 '

    ]);

    $this->_title = $this->_header->append( 'h4', 'Title', [
      'class' => 'modal-title',
      'id' => (string)$id . 'Label'

    ]);

    $button = $this->_header->append( 'button', null, [
      'type' => 'button',
      'class' => 'close',
      bs::data('dismiss', 'modal') => 'modal',
      'aria-label' => 'Close'

    ]);

    $button->append( 'span', '&times;', ['aria-hidden' => 'true' ]);

    $this->_body = $content->append( 'div', null, [
      'class' => 'modal-body',
      'id' => (string)$id . 'Body'

    ]);

    $this->_footer = $content->append( 'div', null, [
      'class' => 'modal-footer',
      'id' => (string)$id . 'Footer'

    ]);

	}

	/* create a modal with a form wrapper */
	static function form( $id = '') {
		self::$FORM_WRAPPER = true;
		return new self( $id);

	}

	public function body() {
		return $this->_body;

	}

	public function dialog() {
		return $this->_dialog;

	}

	public function footer() {
		return $this->_footer;

	}

	public function header() {
		return $this->_header;

	}

	public function title() {
		return $this->_title;

	}

}
