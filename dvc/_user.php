<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	DO NOT change this file
	It is not called directly, the values here are fallback values

	Modify user.php
	*/
namespace dvc;
use dao;

class _user {
	var $name = '';
	var $email = '';

	public function valid() {
		/**
		 * if this function returns true you are logged in
		 */

		return ( true );

	}

	public function isadmin() {
		return ( $this->valid());

	}

	public function sync( oauth $oauth ) {
		sys::logger( '_user::sync => placeholder function - you probably want to write your own' );

	}

	public static function has_uid() {
		return ( isset( $_COOKIE['_bwui']));

	}

	public static function uid() {
		if ( !( isset( $_COOKIE['_bwui'])))
			$uc = md5( sprintf( '%s:%s', userAgent::os(), (string)time()));

		else
			$uc = $_COOKIE['_bwui'];

		$dao = new dao\bwui;
		$dao->getByUID( $uc);

		setcookie( '_bwui', $uc,
			$expires = time()+(60 * 60 * 24 * \config::$COOKIE_AUTHENTICATION_EXPIRES_DAYS),
			$path = '/; samesite=strict',
			$domain = '',
			$secure = true );

		//~ $u = sprintf( '%s:%s', userAgent::os(), $uc);
		return ( $uc);

	}

	public static function hasGoogleFlag() {
		if ( $uid = self::uid()) {
			$uDao = new dao\bwui;
			$uDto = $uDao->getByUID( $uid);
			return ( $uDto->bygoogle);

		}

	}

	public static function setGoogleFlag( $v = 1) {
		if ( $uid = self::uid()) {
			$uDao = new dao\bwui;
			if ( $uDto = $uDao->getByUID( $uid))
				$uDao->UpdateByID( array( 'updated' => db::dbTimeStamp(),'bygoogle' => (int)$v), $uDto->id);


		}

	}

	public static function setUserName( $v = '') {
		if ( $uid = self::uid()) {
			$uDao = new dao\bwui;
			if ( $uDto = $uDao->getByUID( $uid))
				$uDao->UpdateByID( array( 'updated' => db::dbTimeStamp(),'username' => $v), $uDto->id);


		}

	}

	public static function clearGoogleFlag() {
		self::setGoogleFlag(0);

	}

}
