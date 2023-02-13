<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/
	*/

class sqlite extends Controller {
	function index() {
		$p = new dvc\pages\bootstrap('hello world');
			$p
				->header()
				->title();

			$p->primary();

				//------------------------------------------------
				$db = new dvc\sqlite\db();

				$db->Q('DROP TABLE IF EXISTS foo');
				$db->Q('CREATE TABLE IF NOT EXISTS foo ( id INTEGER PRIMARY KEY AUTOINCREMENT, bar STRING)');
				$db->Insert('foo', ['bar' => 'This is a test']);
				$id = $db->Insert('foo', ['bar' => 'This is a test 2']);
				$db->UpdateByID('foo', ['bar' => 'This is a test - number 2'], $id);

				if ( $result = $db->result('SELECT * FROM foo')) {
					while ( $dto = $result->fetch())
						sys::dump( (object)$dto, NULL, FALSE);

				}
				else {
					sys::dump( $db->lastErrorMsg );

				}
				//------------------------------------------------

			$p->secondary();
				print 'secondary';

	}

}
