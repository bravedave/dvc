<?php
/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * This work is licensed under a Creative Commons Attribution 4.0 International Public License.
 *      http://creativecommons.org/licenses/by/4.0/
 *
*/

namespace dvc\core;
use dvc\errsys;
use dvc\strings;
use sys;

abstract class currentUser {
	// THE only instance of the class
	protected static $instance;

	static function user() {
		if ( !isset( self::$instance )) {
			self::$instance = new \user;
			sys::logger( "currentUser::user init", 3 );
			errsys::currentUser( self::$instance->name);

		}

		return ( self::$instance );

	}

	static function valid() {
		return ( self::user()->valid()) ;

	}

	static function isadmin() {
		return ( self::user()->isadmin()) ;

	}

	static function sync( oauth $o) {
		if ( method_exists( self::user(), 'sync'))
			return ( self::user()->sync( $o));

		sys::logger( 'user class does not correctly inherit _user (legacy did not require this, but (e.g.) use of oauth does)');
		return ( false);

	}

	static public function avatar() {
		return \session::get( 'avatar', strings::url( 'images/avatar.png'));

	}

	static public function soAuth() {
		sys::logger( 'soAuth is stub');
		return ( new \imap\soAccount);

	}

	static public function exchangeAuth() {
		throw new Exceptions\exchangeAuthIsAStub;

	}

}
