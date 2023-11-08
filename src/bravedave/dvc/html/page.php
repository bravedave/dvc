<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\dvc\html;

class page extends element {
	protected $head, $body;

	function __construct() {
		parent::__construct('html');
		$this->attributes(array('lang' => 'en'));

		$this->head = new element('head');
		$this->body = new element('body');

		parent::appendChild($this->head);
		parent::appendChild($this->body);
	}

	public function appendChild(element $element): element {

		$this->body->appendChild($element);
		return $this;
	}

	public function addChild(element $element): element {

		$this->appendChild($element);
		return $this;
	}

	function __destruct() {
		print '<!DOCTYPE html>';
		if (!$this->_rendered)
			$this->render();
	}

	function body() {
		return $this->body;
	}
}
