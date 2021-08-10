<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace imap;

class soAccount {
	public $server = '';
	public $account = '';
	public $password = '';
	public $delimiter = '.';
	public $Inbox = 'INBOX';
	public $SentItems = 'Sent';
	public $junkFolder = 'junkmail';
	public $interface = 'imap';
	public $trashFolder = 'Trash';


	public function valid() {
		if ( $this->server == '' || $this->account == '' || $this->password == '')
			return ( false);

		return ( true);

	}

}
