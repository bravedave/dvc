<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/
NameSpace dvc;

abstract class vue {
	static function getBlock( $block) {
		if ( $block) {
			if ( $block = preg_replace( '/[^\da-z_\-]/i', '', $block)) {
				$block .= '.html';

				$path = sprintf( '%s%sapp%svue%s%s',
					\application::app()->getRootPath(),
					DIRECTORY_SEPARATOR,
					DIRECTORY_SEPARATOR,
					DIRECTORY_SEPARATOR,
					$block);

				if ( file_exists( $path)) {
					\sys::serve( $path);
					return;

				}

				$path = sprintf( '%s%svue%s%s',
					__DIR__,
					DIRECTORY_SEPARATOR,
					DIRECTORY_SEPARATOR,
					$block);

				if ( file_exists( $path))
					\sys::serve( $path);

			}

		}

	}

}
