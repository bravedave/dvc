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

class FindFolderResponseMessage {
	//~ protected $_FindFolderResponseType;
	//~ protected $FindFolderResponseMessage;
	//~ protected $RootFolder;

	var $contacts = FALSE;
	var $FolderNames = [];
	var $folders = FALSE;

	function __construct( Response\FindFolderResponseType $response) {
		//~ $this->_FindFolderResponseType = $response;
		$r = array_shift( $response->ResponseMessages->FindFolderResponseMessage);
		//~ $this->FindFolderResponseMessage = $r;
		//~ $this->RootFolder = $r->RootFolder;

		$this->folders = [];
		if ( $r->RootFolder->TotalItemsInView > 0) {
			foreach ( $r->RootFolder->Folders as $folderName => $folder ) {
				$this->FolderNames[] = $folderName;
				foreach ( $folder as $sub ) {
					if ( isset( $sub->FolderId)) {
						$_fldr = (object)[
							'id' => $sub->FolderId->Id,
							'name' => ( $sub->DisplayName ? $sub->DisplayName : $folderName),
							'folderName' => $folderName,

						];

						$this->folders[] = $_fldr;
						if ( $folderName == 'ContactsFolder' && $_fldr->name != 'Suggested Contacts' )
							$this->contacts = $_fldr;

					}

				}

			}

		}

	}

}
