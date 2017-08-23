<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/
NameSpace dao;

class _dbinfo extends _dao {
	protected function check() {
		\sys::logger( 'checking ' . dirname( __FILE__ ) . '/db/*.php' );

		if ( glob( dirname( __FILE__ ) . '/db/*.php')) {
			foreach ( glob( dirname( __FILE__ ) . '/db/*.php') as $f ) {
				\sys::logger( 'checking => ' . $f );
				include_once $f;

			}

		}

	}

	function dump() {
		$this->check();
		$this->db->dump();
		return;

		//~ \sys::logger( "SHOW TABLES FROM " . \config::$DB_NAME );

		if ( $dbR = $this->db->result("SHOW TABLES FROM " . \config::$DB_NAME )) {

			$uID = 0;
			while ( $row = $dbR->fetch_row()) {

				printf( '<span data-role="visibility-toggle" data-target="bqt%s">Table: %s</span><br />%s',
					$uID,
					$row[0],
					PHP_EOL	);
				printf( '<blockquote id=\'bqt%s\' style="font-family: monospace;" class="hidden">%s',
					$uID++,
					PHP_EOL	);

				/* Get field information for all columns */
				if ( $res = $this->db->result( sprintf( 'select * from `%s` LIMIT 1', $this->db->escape( $row[0] )))) {
					$finfo = $res->fetch_fields();

					foreach ($finfo as $val)
						printf( '<br />%s %s (%s)', $val->name, $this->db->field_type( $val->type ), $val->length);

				}

				print "</blockquote>\n";

			}

		}
		else {

			$str = "<pre>
				DB Error, could not list tables
				MySQL Error: " . mysqli_error() . "
				MySQL Host: " . config::$DB_HOST . "
			</pre>";
			echo $str;

		}

	}

}
