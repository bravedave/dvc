<?php
/**
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/
NameSpace dvc\ews;

use \sys;
use \jamesiarmes\PhpEws;
use \jamesiarmes\PhpEws\ArrayType;
use \jamesiarmes\PhpEws\Enumeration;
use \jamesiarmes\PhpEws\Request;
use \jamesiarmes\PhpEws\Type;

abstract class calendar {
	/**
	* Query the exchange calendar and return array of Json
	*
	* return : object
	*/
	static public function agenda( $date = FALSE, $creds = NULL ) {
		if ( $date) {
			if ( is_object( $date)) {
				$dateend = strtotime( $date->end);
				$date = strtotime( $date->start);

			}
			else {
				$dateend = $date = strtotime($date);

			}

		}
		else {
			$dateend = $date = strtotime('today');

		}

		$ret = (object)[
			'dates' => [date( 'D, F j', $date )],
			'datesANSI' => [date( 'Y-m-d', $date )],
			'items' => self::CalendarQuery( $date, $dateend, $creds )
			];

		return ( $ret);

	}

	static protected function Item( $item ) {
		// \sys::dump( $item, NULL, FALSE);
		$x = new calendaritem;
		$x->subject = ( isset( $item->Start ) ? $item->Subject : '' );
		$x->location = ( isset( $item->Location ) ? $item->Location : '' );
		$x->notes = ( isset( $item->Body ) ? $item->Body : '' );
		$x->IsAllDayEvent = ( $item->IsAllDayEvent ? 1 : 0);
		$x->start = ( isset( $item->Start ) ? strtotime( $item->Start ) : '' );
		$x->startUTC = ( isset( $item->Start ) ? date( 'c', strtotime( $item->Start )) : '' );
		$x->end = ( isset( $item->End ) ? strtotime( $item->End ) : '' );
		$x->endUTC = ( isset( $item->End ) ? date( 'c', strtotime( $item->End )) : '' );
		if ( (int)$x->start && (int)$x->end ) {
			//~ \sys::logger(  sprintf( 'exchange->item :: %s - %s', $x->end, $x->start));
			if (  (int)$x->end - (int)$x->start <= 60 )
				$x->timelabel = sprintf( '%s', date( 'h:ia ', $x->start ));
			else
				$x->timelabel = sprintf( '%s - %s', date( 'h:i', $x->start ), date( 'h:i a', $x->end ));

		}

		if ( isset($item->ItemId)) {
			$x->change = $x->id = $item->ItemId->Id;
			$x->changekey = $item->ItemId->ChangeKey;

		}
		$x->item = $item;

		return ( $x );

	}

	static protected function CalendarQuery( $start, $end, $creds = NULL) {
		if ( $ews = client::instance( $creds)) {
			//~ \sys::logger( sprintf( ' CalendarQuery :: %s - %s', $start, $end));
			//~ \sys::logger( sprintf( ' CalendarQuery :: %s : %s',$creds->account, $creds->password));
			//~ \sys::dump( $cred = credentials::getCurrentUser());
			//~ \sys::dump( $ews );

			$request = new Request\FindItemType;
			$request->Traversal = Enumeration\ItemQueryTraversalType::SHALLOW;

			$request->ItemShape = new Type\ItemResponseShapeType;
			// $request->ItemShape->BaseShape = PhpEws\Enumeration\DefaultShapeNamesType::DEFAULT_PROPERTIES;
			/* this will return the notes as well, but adds a significant overhead */
			$request->ItemShape->BaseShape = Enumeration\DefaultShapeNamesType::ALL_PROPERTIES;

			$request->CalendarView = new Type\CalendarViewType();
			$request->CalendarView->StartDate = date("c", strtotime( date("Y-m-d", $start) . " 00:00:00"));	// current time in my timezone
			$request->CalendarView->EndDate = date("c", strtotime( date("Y-m-d", $end) . " 23:59:59"));	// current time in my timezone

			//~ \sys::logger( sprintf( '  CalendarQuery :: date :: %s - %s', $request->CalendarView->StartDate, $request->CalendarView->EndDate));

			$request->ParentFolderIds = new ArrayType\NonEmptyArrayOfBaseFolderIdsType;
			$request->ParentFolderIds->DistinguishedFolderId = new Type\DistinguishedFolderIdType;
			$request->ParentFolderIds->DistinguishedFolderId->Id = Enumeration\DistinguishedFolderIdNameType::CALENDAR;

			$response = $ews->FindItem( $request);

			$ret = [];
			$i = 0;

			try {
				$responseMessage = $response->ResponseMessages->FindItemResponseMessage[0];
				$folder = $responseMessage->RootFolder;
				//~ \sys::dump( $folder);
				if ( $folder->TotalItemsInView > 0 ) {
					//~ if ( $folder->TotalItemsInView == 1 ) {
						//~ foreach( $folder->Items as $item ) {
							//~ if ( $i++ > 50 ) break;
							//~ // \sys::logger( '  @');
							//~ $ret[] = self::item( $item );

						//~ }

					//~ }
					//~ else {
					if ( isset( $folder->Items->CalendarItem)) {
						foreach( $folder->Items->CalendarItem as $item ) {
							if ( $i++ > 500 ) break;
							//~ \sys::logger( '  #' . $i);
							$ret[] = self::item( $item );

						}

					}

				}

			}
			catch ( Exception $e) {
				\sys::trace( 'No Agenda : ResponseCode : ' . $e->getMessage());	//] => NoError
				throw new Exception( $e );

			}

			return ( $ret );

		}

	}

	static protected function Response( $response) {
		/*
		https://msdn.microsoft.com/en-us/library/office/aa564690(v=exchg.140).aspx

		stdClass Object (
			[CreateItemResponseMessage] => stdClass Object (
			or
		    [UpdateItemResponseMessage] => stdClass Object (
				[ResponseCode] => NoError
				[ResponseClass] => Success
				[Items] => stdClass Object (
					[CalendarItem] => stdClass Object (
						[ItemId] => stdClass Object (
							[Id] => AAMkADM5ODIxNDQxLTlhNGQtNDE1NC04YzIyLWU1MzkzY2RlODUyNgBGAAAAAABTRZWuwejfS5GFDFWF1B0RBwCWCMWUAIXmQISwnteyPsw5AAAAqmJZAACWCMWUAIXmQISwnteyPsw5AAAGaS8cAAA=
							[ChangeKey] => DwAAABYAAACWCMWUAIXmQISwnteyPsw5AAAGaT8c

						)
					)
				)
				// UpdateItemResponseMessage has
			    [ConflictResults] => stdClass Object (
				    [Count] => 0
				)
			)
		) */

		if ( $response)
			return new response( $response);

		return ( FALSE);

	}

	static public function CreateAppointment( calendaritem $item, $creds = NULL ) {
		//~ \sys::dump( $item);

		if ( $ews = client::instance( $creds)) {
			$request = new Request\CreateItemType;
			$request->SendMeetingInvitations = Enumeration\CalendarItemCreateOrDeleteOperationType::SEND_TO_NONE;
			//~ $request->SendMeetingInvitations = CalendarItemCreateOrDeleteOperationType::SEND_ONLY_TO_ALL;

			//~ $request->SavedItemFolderId = new TargetFolderIdType();
				//~ $request->SavedItemFolderId->DistinguishedFolderId = new DistinguishedFolderIdType();
				//~ $request->SavedItemFolderId->DistinguishedFolderId->Id = DistinguishedFolderIdNameType::CALENDAR;

			$request->Items = new ArrayType\NonEmptyArrayOfAllItemsType;
				$request->Items->CalendarItem = new Type\CalendarItemType;
					$request->Items->CalendarItem->IsAllDayEvent = false;
					$request->Items->CalendarItem->LegacyFreeBusyStatus = 'Free';
					//~ $request->Items->CalendarItem->Categories->String = $category;

					$request->Items->CalendarItem->Location = $item->location;
					$request->Items->CalendarItem->Subject = $item->subject;
					$request->Items->CalendarItem->Start = $item->startUTC;
					$request->Items->CalendarItem->End = $item->endUTC;

			$_invites = explode( ',', $item->invitees);
			$invitees = [];
			foreach ( (array)$_invites as $e) {
				$em = new \EmailAddress( $e);
				if ( $em->check())
					$invitees[] = $em;

			}

			if ( count( $invitees) > 0) {
				$request->Items->CalendarItem->OptionalAttendees = new ArrayType\NonEmptyArrayOfAttendeesType;
				foreach ( (array)$invitees as $e) {
					\sys::logger( sprintf( 'invitee : %s', $e->email ));
					$attendee = new Type\AttendeeType;
						$attendee->Mailbox = new Type\EmailAddressType;
							$attendee->Mailbox->EmailAddress = $e->email;
							$attendee->Mailbox->RoutingType = Enumeration\RoutingType::SMTP;

					$request->Items->CalendarItem->OptionalAttendees->Attendee[] = $attendee;

				}
				$request->SendMeetingInvitations = Enumeration\CalendarItemCreateOrDeleteOperationType::SEND_TO_ALL_AND_SAVE_COPY;
				//~ $request->SendMeetingInvitations = CalendarItemCreateOrDeleteOperationType::SEND_ONLY_TO_ALL;

			}

			$request->Items->CalendarItem->Body = new Type\BodyType;
				$request->Items->CalendarItem->Body->BodyType = Enumeration\BodyTypeType::TEXT;
				$request->Items->CalendarItem->Body->_ = utf8_encode( $item->notes);

			$response = $ews->CreateItem( $request);
			if ( $response)
				return ( self::Response( $response->ResponseMessages->CreateItemResponseMessage));

		}
		else {
			\sys::logger( sprintf( 'Failed to Create EWS Client::%s', currentUser::name()));

		}
		return ( FALSE );

	}

	static private function field( $uri ) {

		$field = new Type\SetItemFieldType;
			$field->FieldURI = new Type\PathToUnindexedFieldType;
				$field->FieldURI->FieldURI = $uri;
			$field->CalendarItem = new Type\CalendarItemType;
		return ( $field );

	}

	static public function UpdateAppointment( calendaritem $item, $creds = NULL ) {
		if ( $ews = client::instance( $creds)) {
			$request = new Request\UpdateItemType();
				$request->ConflictResolution = Enumeration\ConflictResolutionType::ALWAYS_OVERWRITE;
				//~ $request->SendMeetingInvitationsOrCancellations = PhpEws\Enumeration\CalendarItemUpdateOperationType::SEND_TO_CHANGED_AND_SAVE_COPY;
				$request->SendMeetingInvitationsOrCancellations = Enumeration\CalendarItemUpdateOperationType::SEND_TO_ALL_AND_SAVE_COPY;

			$change = new Type\ItemChangeType();
				$change->ItemId = new Type\ItemIdType();
					$change->ItemId->Id = $item->change;
					$change->ItemId->ChangeKey = $item->changekey;

			$f = self::field( Enumeration\UnindexedFieldURIType::ITEM_SUBJECT);
				$f->CalendarItem->Subject = $item->subject;
				$change->Updates->SetItemField[] = $f;

			$f = self::field( Enumeration\UnindexedFieldURIType::CALENDAR_START);
				$f->CalendarItem->Start = $item->startUTC;
				$change->Updates->SetItemField[] = $f;

			$f = self::field( Enumeration\UnindexedFieldURIType::CALENDAR_END);
				$f->CalendarItem->End = $item->endUTC;
				$change->Updates->SetItemField[] = $f;

			$f = self::field( Enumeration\UnindexedFieldURIType::ITEM_BODY);
				$f->CalendarItem->Body = new Type\BodyType;
				$f->CalendarItem->Body->BodyType = Enumeration\BodyTypeType::TEXT;
				$f->CalendarItem->Body->_ = utf8_encode( $item->notes);
				$change->Updates->SetItemField[] = $f;

			$request->ItemChanges[] = $change;

			$_invites = explode( ',', $item->invitees);
			$invitees = [];
			foreach ( (array)$_invites as $e) {
				$em = new \EmailAddress( $e);
				if ( $em->check())
					$invitees[] = $em;

			}

			if ( count( $invitees) > 0) {
				$f = self::field( Enumeration\UnindexedFieldURIType::CALENDAR_OPTIONAL_ATTENDEES);
					$f->CalendarItem->OptionalAttendees = new ArrayType\NonEmptyArrayOfAttendeesType();
					foreach ( (array)$invitees as $e) {
						\sys::logger( sprintf( 'invitee : %s', $e->email ));
						$attendee = new Type\AttendeeType;
							$attendee->Mailbox = new Type\EmailAddressType;
								$attendee->Mailbox->EmailAddress = $e->email;
								$attendee->Mailbox->RoutingType = Enumeration\RoutingType::SMTP;

						$f->CalendarItem->OptionalAttendees->Attendee[] = $attendee;

					}
					$change->Updates->SetItemField[] = $f;

					//~ $request->ConflictResolution = Request\ConflictResolutionType::AUTO_RESOLVE;
					$request->ConflictResolution = Enumeration\ConflictResolutionType::ALWAYS_OVERWRITE;
					//~ $request->SendMeetingInvitationsOrCancellations = Enumeration\CalendarItemUpdateOperationType::SEND_TO_ALL_AND_SAVE_COPY;
					$request->SendMeetingInvitationsOrCancellations = Enumeration\CalendarItemUpdateOperationType::SEND_TO_CHANGED_AND_SAVE_COPY;

					//~ $change->SendMeetingInvitationsOrCancellationsSpecified = true;

			}

			try {
				//~ if ( currentUser::isdavid())
					//~ \sys::dump( $request, NULL, FALSE);

				$response = $ews->UpdateItem( $request);
				//~ if ( currentUser::isdavid())
					//~ \sys::dump( self::Response( $response->ResponseMessages->UpdateItemResponseMessage));

				if ( $response)
					return ( self::Response( $response->ResponseMessages->UpdateItemResponseMessage));

				return ( FALSE);
				//~ return ( self::Response( $ews->UpdateItem($request)));

			}
			catch ( \Exception $e) {
				//~ if ( currentUser::isdavid()) {
					//~ \sys::dump( $e);

				//~ }
				\dvc\errsys::email_support( $e, 'contained at : dvc\ews\calendar::UpdateAppointment :: EWS Client  Update Failure');
				\sys::logger( 'dvc\ews\calendar::UpdateAppointment :: EWS Client Update Failure' );
				return ( FALSE );

			}

		}
		else {
			\sys::logger( 'Failed to Create EWS Client' );
			return ( FALSE );

		}

	}

}
