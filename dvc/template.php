<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/

namespace dvc;

class template {
	protected $_template = '', $_css = [];

	public $title = '';

	function __construct( $filepath, $css = null) {
		if ( (bool)$css)
		 	$this->_css[] = file_get_contents( $css);

		$this->_template = file_get_contents( $filepath);

	}

	function css( $path) {
		if ( (bool)$path)
		 	$this->_css[] = file_get_contents( $path);

		return ( $this);	// chain

	}

	function replace($var, $content) {
		$this->_template = str_replace( sprintf( '{{%s}}', $var), $content, $this->_template);

		return ( $this);	// chain

	}

	function render() {
		if ( count( $this->_css)) {
			// create instance
			$cssToInlineStyles = new TijsVerkoyen\CssToInlineStyles\CssToInlineStyles();
			// output
			return ( $cssToInlineStyles->convert( sprintf( '<html><head><title>%s</title></head><body>%s</body></html>', $this->title, $this->_template), implode( '', $this->_css)));

		}
		else {
			return( $this->_template);

		}

	}

}
