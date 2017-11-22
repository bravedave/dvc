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


abstract class fields {
	static function deleteHomePhone() {
		$field = new Type\DeleteItemFieldType;
			$field->IndexedFieldURI = new Type\PathToIndexedFieldType;
			$field->IndexedFieldURI->FieldURI = Enumeration\DictionaryURIType::CONTACTS_PHONE_NUMBER;
			$field->IndexedFieldURI->FieldIndex = Enumeration\PhoneNumberKeyType::HOME_PHONE;

		return ( $field);

	}

	static function setHomePhone( $value ) {
		$field = new Type\SetItemFieldType;
			$field->IndexedFieldURI = new Type\PathToIndexedFieldType;
			$field->IndexedFieldURI->FieldURI = Enumeration\DictionaryURIType::CONTACTS_PHONE_NUMBER;
			$field->IndexedFieldURI->FieldIndex = Enumeration\PhoneNumberKeyType::HOME_PHONE;

		$entry = new Type\PhoneNumberDictionaryType;
			$entry->_ = $value;
			$entry->Key = Enumeration\PhoneNumberKeyType::HOME_PHONE;

		$field->Contact = new Type\ContactItemType;
			$field->Contact->PhoneNumbers = new Type\PhoneNumberDictionaryType;
			$field->Contact->PhoneNumbers->Entry = $entry;

		return ( $field);

	}

	static function deleteBusinessPhone() {
		$field = new Type\DeleteItemFieldType;
			$field->IndexedFieldURI = new Type\PathToIndexedFieldType;
			$field->IndexedFieldURI->FieldURI = Enumeration\DictionaryURIType::CONTACTS_PHONE_NUMBER;
			$field->IndexedFieldURI->FieldIndex = Enumeration\PhoneNumberKeyType::BUSINESS_PHONE;

		return ( $field);

	}

	static function setBusinessPhone( $value ) {
		$field = new Type\SetItemFieldType;
			$field->IndexedFieldURI = new Type\PathToIndexedFieldType;
			$field->IndexedFieldURI->FieldURI = Enumeration\DictionaryURIType::CONTACTS_PHONE_NUMBER;
			$field->IndexedFieldURI->FieldIndex = Enumeration\PhoneNumberKeyType::BUSINESS_PHONE;

		$entry = new Type\PhoneNumberDictionaryType;
			$entry->_ = $value;
			$entry->Key = Enumeration\PhoneNumberKeyType::BUSINESS_PHONE;

		$field->Contact = new Type\ContactItemType;
			$field->Contact->PhoneNumbers = new Type\PhoneNumberDictionaryType;
			$field->Contact->PhoneNumbers->Entry = $entry;

		return ( $field);

	}

	static function deleteMobilePhone() {
		$field = new Type\DeleteItemFieldType;
			$field->IndexedFieldURI = new Type\PathToIndexedFieldType;
			$field->IndexedFieldURI->FieldURI = Enumeration\DictionaryURIType::CONTACTS_PHONE_NUMBER;
			$field->IndexedFieldURI->FieldIndex = Enumeration\PhoneNumberKeyType::MOBILE_PHONE;

		return ( $field);

	}

	static function setMobilePhone( $value ) {
		$field = new Type\SetItemFieldType;
			$field->IndexedFieldURI = new Type\PathToIndexedFieldType;
			$field->IndexedFieldURI->FieldURI = Enumeration\DictionaryURIType::CONTACTS_PHONE_NUMBER;
			$field->IndexedFieldURI->FieldIndex = Enumeration\PhoneNumberKeyType::MOBILE_PHONE;

		$entry = new Type\PhoneNumberDictionaryType;
			$entry->_ = $value;
			$entry->Key = Enumeration\PhoneNumberKeyType::MOBILE_PHONE;

		$field->Contact = new Type\ContactItemType;
			$field->Contact->PhoneNumbers = new Type\PhoneNumberDictionaryType;
			$field->Contact->PhoneNumbers->Entry = $entry;

		return ( $field);

	}

	static function deleteEmailAddress1() {
		$field = new Type\DeleteItemFieldType;
			$field->IndexedFieldURI = new Type\PathToIndexedFieldType;
			$field->IndexedFieldURI->FieldURI = Enumeration\DictionaryURIType::CONTACTS_EMAIL_ADDRESS;
			$field->IndexedFieldURI->FieldIndex = Enumeration\EmailAddressKeyType::EMAIL_ADDRESS_1;

		return ( $field);

	}

	static function setEmailAddress1( $value ) {
		$field = new Type\SetItemFieldType;
			$field->IndexedFieldURI = new Type\PathToIndexedFieldType;
			$field->IndexedFieldURI->FieldURI = Enumeration\DictionaryURIType::CONTACTS_EMAIL_ADDRESS;
			$field->IndexedFieldURI->FieldIndex = Enumeration\EmailAddressKeyType::EMAIL_ADDRESS_1;

		$entry = new Type\EmailAddressDictionaryEntryType;
			$entry->_ = $value;
			$entry->Key = Enumeration\EmailAddressKeyType::EMAIL_ADDRESS_1;

		$field->Contact = new Type\ContactItemType;
			$field->Contact->EmailAddresses = new Type\EmailAddressDictionaryType;
			$field->Contact->EmailAddresses->Entry = $entry;

		return ( $field);

	}

	static function deleteEmailAddress2() {
		$field = new Type\DeleteItemFieldType;
			$field->IndexedFieldURI = new Type\PathToIndexedFieldType;
			$field->IndexedFieldURI->FieldURI = Enumeration\DictionaryURIType::CONTACTS_EMAIL_ADDRESS;
			$field->IndexedFieldURI->FieldIndex = Enumeration\EmailAddressKeyType::EMAIL_ADDRESS_2;

		return ( $field);

	}

	static function setEmailAddress2( $value ) {
		$field = new Type\SetItemFieldType;
			$field->IndexedFieldURI = new Type\PathToIndexedFieldType;
			$field->IndexedFieldURI->FieldURI = Enumeration\DictionaryURIType::CONTACTS_EMAIL_ADDRESS;
			$field->IndexedFieldURI->FieldIndex = Enumeration\EmailAddressKeyType::EMAIL_ADDRESS_2;

		$entry = new Type\EmailAddressDictionaryEntryType;
			$entry->_ = $value;
			$entry->Key = Enumeration\EmailAddressKeyType::EMAIL_ADDRESS_2;

		$field->Contact = new Type\ContactItemType;
			$field->Contact->EmailAddresses = new Type\EmailAddressDictionaryType;
			$field->Contact->EmailAddresses->Entry = $entry;

		return ( $field);

	}

}

