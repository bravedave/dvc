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
use \jamesiarmes\PhpEws\ArrayType;
use \jamesiarmes\PhpEws\Enumeration;
use \jamesiarmes\PhpEws\Request;
use \jamesiarmes\PhpEws\Type;

abstract class query {
	static public function GetItemByID( $itemID, $creds = NULL) {
		if ( $ews = client::instance( $creds)) {
			// Build the request for the parts.
			$request = new Request\GetItemType;

			$request->ItemShape = new Type\ItemResponseShapeType;
			$request->ItemShape->BaseShape = Enumeration\DefaultShapeNamesType::ALL_PROPERTIES;
			// You can get the body as HTML, text or "best".
			//~ $request->ItemShape->BodyType = PhpEws\Enumeration\BodyTypeResponseType::BEST;
			$request->ItemShape->BodyType = Enumeration\BodyTypeResponseType::TEXT;

			// Add the body property.
			$body_property = new Type\PathToUnindexedFieldType;
			$body_property->FieldURI = Enumeration\UnindexedFieldURIType::ITEM_BODY;

			$request->ItemShape->AdditionalProperties = new ArrayType\NonEmptyArrayOfPathsToElementType;
			$request->ItemShape->AdditionalProperties->FieldURI = [ $body_property ];

			$request->ItemIds = new ArrayType\NonEmptyArrayOfBaseItemIdsType;
			$request->ItemIds->ItemId = [];

			// Add the message to the request.
			$message_item = new Type\ItemIdType;
			$message_item->Id = $itemID;
			$request->ItemIds->ItemId[] = $message_item;

			//~ sys::logger( $itemID);

			try {
				$response = $ews->GetItem( $request);
				if ( is_array( $response->ResponseMessages->GetItemResponseMessage)) {
					$o = array_shift( $response->ResponseMessages->GetItemResponseMessage);
					return ( $o->Items);

				}
				else {
					return ( FALSE);

				}

			}
			catch (Exception $e) {
				sys::logger( "Could Not Get Body of Agenda : ResponseCode:" . print_r( $response, TRUE ));	//] => NoError
				//~ throw new Exception( $e );

			}

			try {
				$item->notes = $response->ResponseMessages->GetItemResponseMessage->Items->CalendarItem->Body->_;

			}
			catch (Exception $e) {
				sys::logger( "Could Not Get Body of Agenda : ResponseCode:" . print_r( $response, TRUE ));	//] => NoError
				//~ throw new Exception( $e );

			}

		}
		else {
			throw new Exceptions\FailedtoCreateEWSClient;

		}

	}

	static public function GetCalendarItemByID( $itemID, $creds = NULL) {
		$items = self::GetItemByID( $itemID, $creds) ;

		if ( isset( $items->CalendarItem))
			return ( array_shift( $items->CalendarItem));

		return ( FALSE);

	}

	static public function DeleteItem( $itemID, $creds = NULL ) {
		if ( $ews = client::instanceForDelete( $creds)) {
			$request = new Request\DeleteItemType;

			// Identify the items to delete.
			$request->ItemIds = new ArrayType\NonEmptyArrayOfBaseItemIdsType;
				$request->ItemIds->ItemId = new Type\ItemIdType;
					$request->ItemIds->ItemId->Id = $itemID;

			// Identify how deleted items are handled.
			$request->DeleteType = Enumeration\DisposalType::MOVE_TO_DELETED_ITEMS;
			$request->AffectedTaskOccurrences = Enumeration\AffectedTaskOccurrencesType::SPECIFIED;
			//~ $request->AffectedTaskOccurrencesSpecified = TRUE;

			$request->SendMeetingCancellations = Enumeration\CalendarItemCreateOrDeleteOperationType::SEND_ONLY_TO_ALL;
			//~ $request->SendMeetingCancellationsSpecified = TRUE;

			//~ throw new \Exception( "I\'m still working on this" );
			//~ \sys::dump( $request);
			$response = $ews->DeleteItem($request);
			\sys::logger( 'dvc\ews\query :: DeleteItem :: deleted item');
			return ( TRUE );

		}
		else {
			throw new Exceptions\FailedtoCreateEWSClient;

		}

	}

	static public function getFolderIds( $creds = NULL) {
		$ret = [];

		$request = new Request\FindFolderType;
			$request->Traversal = Enumeration\FolderQueryTraversalType::SHALLOW;
			// $request->Traversal = Enumeration\FolderQueryTraversalType::DEEP;	// subfolders too

		$request->FolderShape = new Type\FolderResponseShapeType;
			// Options => ALL_PROPERTIES, DEFAULT_PROPERTIES, ID_ONLY
			// $request->FolderShape->BaseShape = Enumeration\DefaultShapeNamesType::ALL_PROPERTIES;
			$request->FolderShape->BaseShape = Enumeration\DefaultShapeNamesType::DEFAULT_PROPERTIES;
			//~ $request->FolderShape->BaseShape = Enumeration\DefaultShapeNamesType::ID_ONLY;

		// configure the view
		$request->IndexedPageFolderView = new Type\IndexedPageViewType;
			$request->IndexedPageFolderView->BasePoint = Enumeration\IndexBasePointType::BEGINNING;
			$request->IndexedPageFolderView->Offset = 0;

		$request->ParentFolderIds = new ArrayType\NonEmptyArrayOfBaseFolderIdsType();

			// use a distinguished folder name to find folders inside it
			$request->ParentFolderIds->DistinguishedFolderId = new Type\DistinguishedFolderIdType;
				$request->ParentFolderIds->DistinguishedFolderId->Id = Enumeration\DistinguishedFolderIdNameType::MESSAGE_ROOT;

		if ( $ews = client::instance( $creds)) {
			try
				{ return new response\FindFolderResponseMessage( $ews->FindFolder($request)); }

			catch (Exception $e)
				{ sys::logger( "Exception : $e" ); }

			return ( $ret );

		}
		else
			{ throw new Exceptions\FailedtoCreateEWSClient; }

	}

}
