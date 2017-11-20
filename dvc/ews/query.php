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

	}

	static public function GetCalendarItemByID( $itemID, $creds = NULL) {
		$items = self::GetItemByID( $itemID, $creds) ;

		if ( isset( $items->CalendarItem))
			return ( array_shift( $items->CalendarItem));

		return ( FALSE);

	}

}
