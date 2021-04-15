<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace beds_list;

class config extends \config {
	const green_beds_list_db_version = 0.01;

  const label = 'Beds List';

  static protected $_GREEN_BEDS_LIST_VERSION = 0;

	static protected function green_beds_list_version( $set = null) {
		$ret = self::$_GREEN_BEDS_LIST_VERSION;

		if ( (float)$set) {
			$config = self::green_beds_list_config();

			$j = file_exists( $config) ?
				json_decode( file_get_contents( $config)):
				(object)[];

			self::$_GREEN_BEDS_LIST_VERSION = $j->green_beds_list_version = $set;

			file_put_contents( $config, json_encode( $j, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

		}

		return $ret;

	}

	static function green_beds_list_checkdatabase() {
		if ( self::green_beds_list_version() < self::green_beds_list_db_version) {
      $dao = new dao\dbinfo;
			$dao->dump( $verbose = false);

			config::green_beds_list_version( self::green_beds_list_db_version);

		}

		// sys::logger( 'bro!');

	}

	static function green_beds_list_config() {
		return implode( DIRECTORY_SEPARATOR, [
      rtrim( self::dataPath(), '/ '),
      'green_beds_list.json'

    ]);

	}

  static function green_beds_list_init() {
		if ( file_exists( $config = self::green_beds_list_config())) {
			$j = json_decode( file_get_contents( $config));

			if ( isset( $j->green_beds_list_version)) {
				self::$_GREEN_BEDS_LIST_VERSION = (float)$j->green_beds_list_version;

			};

		}

	}

}

config::green_beds_list_init();
