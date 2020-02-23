<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	This is the Bootstrap 4 modal

	php usage:
		$m = new dvc\pages\modal;
		$m->open();
		// implement page

	incorporated into control
		$this->modal( 'page-to-load');

	javascript usage:
	_cms_.modal({
		url : <url-to-load>,
		onSuccess : function() {
			// callback ..
		}

	});

	_cms_.modal({
		url : _cms_.url('sms/modal'),
		onSuccess : function() {
			// callback ..

		}

	})
	.then( function() {
		$('input[name="to[]"]').val('0418745334');

	});
	*/
namespace dvc\pages;

class modal {
	protected $title = '';
	protected $className = '';
	protected $classHeader = '';
	protected $_open = false;

	function __construct( $params = []) {
		$_options = [
			'title' => 'modal',
			'class' => '',
			'header-class' => 'text-white bg-secondary',

		];

		$options = array_merge( $_options, $params);

		$this->title = $options['title'];
		$this->className = $options['class'];
		$this->classHeader = $options['header-class'];

	}

	function __destruct() {
		$this->close();

	}

	function open() {
		if ( $this->_open) {
			return;

		}

		printf( '<div class="modal" tabindex="-1" role="dialog">
	<div class="modal-dialog %s modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header %s">
				<h5 class="modal-title">%s</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>

				</button>

			</div>

			<div class="modal-body">', $this->className, $this->classHeader, $this->title);

		$this->_open = true;

	}

	function close() {
		if ( !$this->_open) {
			return;

		}

		printf( '%s%s			</div>%s', PHP_EOL, PHP_EOL, PHP_EOL);	//<!-- div class="modal-body" -->

		printf( '%s		</div>%s', PHP_EOL, PHP_EOL);	// <!-- div class="modal-content" -->

		printf( '%s	</div>%s', PHP_EOL, PHP_EOL);	// <!-- div class="modal-dialog" role="document" -->

		printf( '%s</div>%s', PHP_EOL, PHP_EOL);	//<!-- div class="body" -->

		$this->_open = false;

	}

}
