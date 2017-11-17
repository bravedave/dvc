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

class calendar {
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
		$x = new calendaritem();
		$x->subject = ( isset( $item->Start ) ? $item->Subject : '' );
		$x->location = ( isset( $item->Location ) ? $item->Location : '' );
		$x->notes = ( isset( $item->Body ) ? $item->Body : '' );
		$x->start = ( isset( $item->Start ) ? strtotime( $item->Start ) : '' );
		$x->startUTC = ( isset( $item->Start ) ? date( 'c', strtotime( $item->Start )) : '' );
		$x->end = ( isset( $item->End ) ? strtotime( $item->End ) : '' );
		$x->endUTC = ( isset( $item->End ) ? date( 'c', strtotime( $item->End )) : '' );
		if ( (int)$x->start && (int)$x->end ) {
			//~ sys::logger(  sprintf( 'exchange->item :: %s - %s', $x->end, $x->start));
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
			//~ sys::logger( sprintf( ' CalendarQuery :: %s : %s',$creds->account, $creds->password));
			//~ \sys::dump( $cred = credentials::getCurrentUser());
			//~ \sys::dump( $ews );

			$request = new PhpEws\Request\FindItemType;
			$request->Traversal = PhpEws\Enumeration\ItemQueryTraversalType::SHALLOW;

			$request->ItemShape = new PhpEws\Type\ItemResponseShapeType;
			$request->ItemShape->BaseShape = PhpEws\Enumeration\DefaultShapeNamesType::DEFAULT_PROPERTIES;
			/* this will return the notes as well, but adds a significant overhead */
			//~ $request->ItemShape->BaseShape = PhpEws\Enumeration\DefaultShapeNamesType::ALL_PROPERTIES;

			$request->CalendarView = new PhpEws\Type\CalendarViewType();
			$request->CalendarView->StartDate = date("c", strtotime( date("Y-m-d", $start) . " 00:00:00"));	// current time in my timezone
			$request->CalendarView->EndDate = date("c", strtotime( date("Y-m-d", $end) . " 23:59:59"));	// current time in my timezone

			//~ sys::logger( sprintf( '  CalendarQuery :: date :: %s - %s', $request->CalendarView->StartDate, $request->CalendarView->EndDate));

			$request->ParentFolderIds = new PhpEws\ArrayType\NonEmptyArrayOfBaseFolderIdsType;
			$request->ParentFolderIds->DistinguishedFolderId = new PhpEws\Type\DistinguishedFolderIdType;
			$request->ParentFolderIds->DistinguishedFolderId->Id = PhpEws\Enumeration\DistinguishedFolderIdNameType::CALENDAR;

			$response = $ews->FindItem( $request);

			$ret = [];
			$i = 0;

			try {
				$responseMessage = $response->ResponseMessages->FindItemResponseMessage[0];
				$folder = $responseMessage->RootFolder;
				//~ sys::dump( $folder);
				if ( $folder->TotalItemsInView > 0 ) {
					if ( $folder->TotalItemsInView == 1 ) {
						foreach( $folder->Items as $item ) {
							if ( $i++ > 50 ) break;
							//~ sys::logger( '  @');
							$ret[] = self::item( $item );

						}

					}
					else {
						foreach( $folder->Items->CalendarItem as $item ) {
							if ( $i++ > 500 ) break;
							//~ sys::logger( '  #' . $i);
							$ret[] = self::item( $item );

						}

					}

				}

			}
			catch ( Exception $e) {
				sys::trace( 'No Agenda : ResponseCode : ' . $e->getMessage());	//] => NoError
				throw new Exception( $e );

			}

			return ( $ret );

		}

	}

}
