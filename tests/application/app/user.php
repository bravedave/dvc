<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

class user extends dvc\user {
  public function valid() {
		/**
		 * if this function returns true you are logged in
		 */
    return ( dvc\session::get( 'valid'));

	}

}
