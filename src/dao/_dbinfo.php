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
	protected function check() {
		if ( $glob = glob( dirname( __FILE__ ) . '/db/*.php')) {
			foreach ( $glob as $f ) {
				\sys::logger( 'checking => ' . $f );
				include_once $f;

			}

		}

	}

	function dump( $verbose = true) {
		$this->check();
		if ( (bool)$verbose) $this->db->dump();

	}

}
