<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\dvc;

abstract class load {
	protected static $_loaderCount = 0;
	protected static $_loaderCounter = null;
    protected static $_logloader = 0;

	static public function logloaderon( $b) {
		self::$_logloader = (bool)$b;

    }

	static public function loaderCounter( hitter $hitter) {
		self::$_loaderCounter = $hitter;

    }

    static public function logger( $v) {
		self::$_loaderCount++;

		if ( self::$_loaderCounter)
			self::$_loaderCounter->hits( self::$_loaderCount);

		if ( (bool)self::$_logloader)
			error_log( sprintf( '%d. %s', self::$_loaderCount, $v));

    }

}
