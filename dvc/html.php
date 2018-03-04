<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/
Namespace dvc;

abstract class html {
	protected static function _icon( $s, $attributes = NULL) {
		if ( strlen( (string)$s) <= 2)
			return ( html\initialicon::rendered( $s, $attributes));
		else
			return ( html\initialicon::rendered( strings::initials( $s), $attributes));

	}

	public static function icon( $s, $title = NULL) {
		if ( is_null( $title))
			return ( self::_icon( $s));

		$attribs = $title;
		if ( !is_array( $title))
			$attribs = [ 'title' => $title];

		return ( self::_icon( $s, $attribs));

	}

	public static function modal( $id = 'myModal' ) {
		return ( new html\modal( $id));

	}

	public static function appendHTML( \DOMNode $parent, $source) {
		$tmpDoc = new \DOMDocument();
		$tmpDoc->loadHTML($source);

		foreach ($tmpDoc->getElementsByTagName('body')->item(0)->childNodes as $node) {
			$node = $parent->ownerDocument->importNode( $node, TRUE);
			$parent->appendChild( $node);

		}


	}

}
