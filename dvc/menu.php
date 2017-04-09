<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/
NameSpace dvc;

abstract class menu {
	static public function build( $a ) {

		$s = '<ul class="menu">';
		foreach ( $a as $item )
			$s .= $item->resolve() . PHP_EOL;

		$s .= '</ul>';

		return ( $s );

	}

	static public function MainContextMenu() {}

}
