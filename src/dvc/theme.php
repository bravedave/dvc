<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc;

class theme {
	static function navbar( $params = []) {
		$options = array_merge([
      'color' => 'navbar-light bg-light',
			'defaults' => 'navbar navbar-expand-md d-print-none',
			'sticky' => 'sticky-top',
		], $params);

    return implode( ' ', $options);

	}

	static function modalHeader() {
		return 'text-white bg-primary';

	}

}
