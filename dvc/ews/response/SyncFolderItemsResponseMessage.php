<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/
NameSpace dvc\ews\response;

use \jamesiarmes\PhpEws;
use \jamesiarmes\PhpEws\ArrayType;
use \jamesiarmes\PhpEws\Enumeration;
use \jamesiarmes\PhpEws\Request;
use \jamesiarmes\PhpEws\Response;
use \jamesiarmes\PhpEws\Type;

class SyncFolderItemsResponseMessage {
	//~ protected $SyncFolderItemsResponseMessage;
	var $changes = NULL;
	var $SyncState = NULL;

	function __construct( Response\SyncFolderItemsResponseType $response) {
		$r = array_shift( $response->ResponseMessages->SyncFolderItemsResponseMessage);
		//~ $this->SyncFolderItemsResponseMessage = $r;

		$this->changes = $r->Changes;
		$this->SyncState = $r->SyncState;

	}

}