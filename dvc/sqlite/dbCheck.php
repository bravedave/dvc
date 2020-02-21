<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	The New Checking Function
	*/
namespace dvc\sqlite;

class dbCheck {	// extends _dao {
	protected $table;
	protected $pk = "id";
	protected $structure = [];
	protected $indexs = [];
	protected $db;

	function __construct( db $db = null, $table, $pk = "id" ) {
		$this->db = $db;
		//~ parent::__construct( $db );

		$this->table = $table;
		$this->pk = $pk;

	}

	/*
	 * INTEGER
	 * REAL
	 * TEXT
	 * BLOB
	 */
	function defineField( $name = "", $type = "TEXT", $default = "" ) {
		if ( $name == "" )
			return ( false );

		if ( strtolower( $type) == 'bigint')
			$type = 'INTEGER';
		elseif ( in_array( strtolower( $type), ['int','bigint','tinyint']) !== false)
			$type = 'INTEGER';
		elseif ( in_array( strtolower( $type), ['blob','mediumblob','longblob']) !== false)
			$type = 'BLOB';
		elseif ( in_array( strtolower( $type), ['varchar','mediumtext','date','datetime']) !== false)
			$type = 'TEXT';
		elseif ( in_array( strtolower( $type), ['float','double']) !== false)
			$type = 'REAL';

		$this->structure[] = [
			'name' => $name,
			'type' => strtoupper( $type),
			'default' => $default

		];

	}

	function defineIndex( $key, $field ) {
		$this->indexs[] = [
			'key' => $key,
			'field' => $field

		];

	}

	function check() {
		$fields = [$this->pk . ' INTEGER PRIMARY KEY AUTOINCREMENT'];
		foreach ( $this->structure as $fld ) {

			if ( $fld["type"] == 'INTEGER' )
				$fields[] = sprintf( '`%s` INTEGER DEFAULT %s', $fld['name'], (int)$fld['default']);

			elseif ( $fld["type"] == 'REAL' )
				$fields[] = sprintf( '`%s` REAL DEFAULT %s', $fld['name'], (int)$fld['default']);

			elseif ( $fld["type"] == 'TEXT' ) {
				/**
				 * to be compatible with MySQL set collation to insensitive
				 */
				$fields[] = sprintf( '`%s` TEXT COLLATE NOCASE', $fld['name']);

			}
			elseif ( $fld["type"] == 'BLOB' )
				$fields[] = sprintf( '`%s` BLOB', $fld['name']);

			else
				throw new \Exception( 'unknown field type dbCheck => check -> ' . $fld['type'] );

		}

		$sql = sprintf( 'CREATE TABLE IF NOT EXISTS `%s`(%s)', $this->table, implode( ',', $fields ));
		//~ print "<pre>" . print_r( $fields, TRUE ) . "</pre>";
		//~ print $sql;
		$this->db->Q( $sql );

		$fieldList = $this->db->fieldList( $this->table);
		$fields = [];
		foreach ( $fieldList as $f)
			$fields[] = $f->name;

		foreach ( $this->structure as $fld ) {
			if ( !in_array( $fld['name'], $fields )) {
				if ( $fld['type'] == 'INTEGER' )
					$sql = sprintf( 'ALTER TABLE `%s` ADD COLUMN `%s` INTEGER DEFAULT %s', $this->table, $fld['name'], (int)$fld['default']);

				elseif ( $fld['type'] == 'REAL' )
					$sql = sprintf( 'ALTER TABLE `%s` ADD COLUMN `%s` REAL DEFAULT %s', $this->table, $fld['name'], (int)$fld['default']);

				elseif ( $fld['type'] == 'TEXT' )
					$sql = sprintf( 'ALTER TABLE `%s` ADD COLUMN `%s` TEXT', $this->table, $fld['name']);

				elseif ( $fld['type'] == 'BLOB' )
					$sql = sprintf( 'ALTER TABLE `%s` ADD COLUMN `%s` BLOB', $this->table, $fld['name']);

				else
					throw new \Exception( 'unknown field type dbCheck => check -> ' . $fld['type'] );

				//~ \sys::dump( $fields, NULL, FALSE);
				//~ \sys::dump( $sql);

				$this->db->Q( $sql );

			}

		}

		foreach ( $this->indexs as $index ) {
			$sql = sprintf( 'CREATE INDEX IF NOT EXISTS `%s` ON `%s` (%s)',
				$this->db->escape( $index['key'] ),
				$this->db->escape( $this->table ),
				$this->db->escape( $index['field'] ) );
			$this->db->Q( $sql);

		}

	}

}
