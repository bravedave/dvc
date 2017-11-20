<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/

NameSpace dvc\ews;

use \jamesiarmes\PhpEws;
use \jamesiarmes\PhpEws\Enumeration;

class response {
	public $ResponseCode = '',
		$ResponseClass = '',
		$ResponseType = 'unknown',
		$ResponseMessage = '',
		$Id = FALSE,
		$ChangeKey = FALSE;

	public function __construct( $ResponseMessage) {
		//~ \sys::logger( '-------------------------');
		//~ \sys::logger( print_r( $ResponseMessage, TRUE));
		//~ \sys::logger( '-------------------------');

		if ( isset( $ResponseMessage[0]))
			$ResponseMessage = $ResponseMessage[0];

		//~ if ( currentUser::isdavid()) {
			//~ \sys::dump( $ResponseMessage, NULL, TRUE);
			//~ \sys::dump( self::Response( $response));

		//~ }


		$this->ResponseCode = $ResponseMessage->ResponseCode;
		$this->ResponseClass = $ResponseMessage->ResponseClass;
		$this->ResponseMessage = $ResponseMessage->MessageText;

		if ($this->ResponseClass != Enumeration\ResponseClassType::SUCCESS) {
			\sys::logger( sprintf( 'xChangeResponse failed with "%s: %s"', $this->ResponseCode, $this->ResponseMessage));

		}

		if ( isset( $ResponseMessage->Items)) {
			if ( isset( $ResponseMessage->Items->CalendarItem)) {
				$ci = $ResponseMessage->Items->CalendarItem[0];

				//~ \sys::logger( print_r( $ci, TRUE));
				//~ \sys::logger( '-------------------------');
				$this->ResponseType = 'CalendarItem';
				if ( isset( $ci->ItemId)) {
					if ( isset( $ci->ItemId->Id)) {
						$this->Id = $ci->ItemId->Id;
						$this->ChangeKey = $ci->ItemId->ChangeKey;

					}
					else {
						\sys::logger( "xChangeResponse / CalendarItem : not ItemId->id");

					}

				}
				else {
					\sys::logger( "xChangeResponse / CalendarItem : not ItemId");

				}

			}
			else {
				\sys::logger( "xChangeResponse / not CalendarItem : " . print_r( $ResponseMessage, TRUE));

			}

		}
		else {
			\sys::logger( "xChangeResponse / no items");

		}

	}

	public function tostring() {
		return ( sprintf( '%s : %s - %s', $this->ResponseCode, $this->ResponseClass, $this->ResponseType));

	}

}
