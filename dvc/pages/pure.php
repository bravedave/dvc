<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	DO NOT change this file
	Copy it to <application>/app/dvc/ and modify it there
	*/
NameSpace dvc\pages;

class pure extends _page {
	function __construct( $title = '' ) {
		parent::__construct( $title );

		array_unshift( $this->css, sprintf( '<link type="text/css" rel="stylesheet" media="all" href="%s" />', \url::$URL . 'pure/pure-min.css' ));
		//~ $this->latescripts[] = sprintf( '<script src="%s"></script>', url::$URL . 'js/bootstrap.min.js' );

	}

	public function title( $navbar = NULL) {
		printf( '
	<div class="topbar pure-menu pure-menu-horizontal" role="navigation">
		<a  class="pure-menu-heading pure-menu-link" href="%s">%s</a>
	</div>%s',
		\url::$URL,
		$this->title,
		PHP_EOL );

	}

	protected function openContent() {
		if ( $this->contentOPEN )
			return;

		$this->pageHeader();

		print <<<OUTPUT

	<div class="main-content-wrapper pure-g"><!-- Main Content Area -->

OUTPUT;
		$this->contentOPEN = TRUE;

		return ( $this);

	}

	public function primary( $class = 'sidebar pure-u-1 pure-u-md-1-4') {
		parent::primary( $class);

	}

	public function secondary( $class = 'content pure-u-1 pure-u-md-3-4' ) {
		parent::secondary( $class);

	}

	public function pagefooter() {
		$this
			->header()
			->pageHeader();

		$footer = new \dvc\html\element( 'footer' );

			$div = $footer->append( 'div', NULL, array( 'class' => 'pure-g' ));

				$div->append( 'div', NULL, array( 'class' => 'pure-u-2-3'));
				$div->append( 'div',
					'<a title="software by BrayWorth using php" href="http://brayworth.com.au" target="_blank">BrayWorth</a>',
					array(
						'class' => 'pure-u-1-3 text-center noprint',
						'id' => 'brayworthLOGO' ));

	}

}
