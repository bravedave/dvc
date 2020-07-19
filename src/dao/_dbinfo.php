<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dao;

class _dbinfo extends _dao {
	protected function checkDIR( $dir) {
		\sys::logger( sprintf('<checking %s> %s', $dir, __METHOD__));
		if ( $glob = glob( $dir . '/db/*.php')) {
			foreach ( $glob as $f ) {
				\sys::logger( sprintf('<checking => %s> %s', $f, __METHOD__));
				include_once $f;

			}

		}

	}

	protected function check() {
		$this->checkDIR( __DIR__);

	}

	function dump( $verbose = true) {
		$this->check();
		if ( (bool)$verbose) $this->db->dump();

	}

}
