<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	The New Checking Function

	*/
NameSpace dao;

class dbCheck extends _dao {
	protected $table;
	protected $pk = "id";
	protected $structure = Array();
	protected $indexs = Array();

	function __construct( \dvc\db $db = NULL, $table, $pk = "id" ) {
		parent::__construct( $db );

		$this->table = $table;
		$this->pk = $pk;

	}

	function defineField( $name = "", $type = "varchar", $len = NULL, $dec = 0, $default = "" ) {
		if ( $name == "" )
			return ( FALSE );

		if ( $type == "date" && $default == "" )
			$default = "0000-00-00";
		if ( $type == "datetime" && $default == "" )
			$default = "0000-00-00 00:00:00";
		if ( ( $type == "int" || $type == "bigint" || $type == "double" || $type == "float" ) && $default == "" )
			$default = "0";

		if ( ( $type == "int" || $type == "bigint" || $type == "double" || $type == "float" ) && ( is_null( $len) || (int)$len < 1))
			$len = 20;

		if ( is_null( $len) || (int)$len < 1)
			$len = 45;

		$this->structure[] = Array(
			"name" => $name,
			"type" => $type,
			"length" => $len,
			"decimal" => $dec,
			"default" => $default );

	}

	function defineIndex( $key, $field ) {
		$this->indexs[] = Array(
			'key' => $key,
			'field' => $field );

	}

	function check() {

		$fields = array( $this->pk . " bigint(20) NOT NULL auto_increment" );
		foreach ( $this->structure as $fld ) {

			if ( $fld["type"] == "varchar" ) {
				$fields[] = "`" . $fld["name"] . "` varchar(" . (string)$fld["length"] . ") default '" . $this->db->escape( $fld["default"] ) . "'";

			}
			elseif ( $fld["type"] == "date" || $fld["type"] == "datetime" ) {
				$fields[] = "`" . $fld["name"] . "` " . $fld["type"] . " default '" . $this->db->escape( $fld["default"] ) . "'";

			}
			elseif ( $fld["type"] == "timestamp" ) {
				$fields[] = "`" . $fld["name"] . "` " . $fld["type"];

			}
			elseif ( $fld["type"] == "text" ) {
				$fields[] = "`" . $fld["name"] . "` text";

			}
			elseif ( $fld["type"] == "bigint" ) {
				$fields[] = "`" . $fld["name"] . "` bigint(" . (string)$fld["length"] . ") default '" . (int)$fld["default"] . "'";

			}
			elseif ( $fld["type"] == "tinyint" ) {
				$fields[] = "`" . $fld["name"] . "`  tinyint(1) default 0";

			}
			elseif ( $fld["type"] == "int" ) {
				$fields[] = "`" . $fld["name"] . "`  int default '" . (int)$fld["default"] . "'";

			}
			elseif ( $fld["type"] == "double" ) {
				$fields[] = "`" . $fld["name"] . "`  double default '" . (int)$fld["default"] . "'";

			}
			elseif ( $fld["type"] == "float" ) {
				$fields[] = "`" . $fld["name"] . "`  float default '" . (int)$fld["default"] . "'";

			}
			elseif ( $fld["type"] == "blob" ) {
				$fields[] = "`" . $fld["name"] . "`  blob";

			}
			elseif ( $fld["type"] == "mediumblob" ) {
				$fields[] = "`" . $fld["name"] . "`  mediumblob";

			}
			else {
				die( "unknown field type dbCheck => check -> " . $fld["type"] );

			}

		}

		$fields[] = "PRIMARY KEY  (`" . $this->pk . "`)";
		foreach ( $this->indexs as $key )
			$fields[] = " KEY `" . $key["key"] . "` (" . $key["field"] . ")";

		$sql = "CREATE TABLE IF NOT EXISTS `" . $this->table  . "`( " . implode( ",", $fields ) .  " )";
		//~ print "<pre>" . print_r( $fields, TRUE ) . "</pre>";
		//~ print $sql;
		$this->db->Q( $sql );

		$fields = $this->db->fieldList( $this->table );
		$fieldStructures = $this->db->fetchFields( $this->table );
		$charset = $this->db->getCharSet();
		$after = "";
		foreach ( $this->structure as $fld ) {
			if ( in_array( $fld["name"], $fields )) {
				if ( $fld["type"] == "varchar" ) {
					if ( \config::$DB_ALTER_FIELD_STRUCTURES) {
						/*---[ we want to know if we should alter the field ]---*/
						// get structure for this field
						if ( $charset = 'utf8') {
							foreach ( $fieldStructures as $fieldStructure ) {
								if ($fieldStructure->name == $fld["name"]) {
									$fieldLength = ( (int)$fieldStructure->length / 3);	// utf8 conversion
									if ( $fieldLength < $fld["length"]) {
										//~ \sys::logger( sprintf( 'bingo baby :: %s : %s != %s', $fieldStructure->name, $fieldStructure->length, $fld["length"]));
										$sql = sprintf( 'ALTER TABLE `%s` CHANGE COLUMN `%s` `%s` varchar(%s) default "%s"',
											$this->table, $fld["name"],
											$fld["name"], (string)$fld["length"], $this->db->escape( $fld["default"] ));

										\sys::logSQL( $sql);

									}

									break;

								}

							}

						}
						/*---[ end: we want to know if we should alter the field ]---*/

					}

				}

			}
			else {
				if ( $fld["type"] == "varchar" ) {
					$sql = "alter table `" . $this->table . "` add column `" . $fld["name"] .
						"` varchar(" . (string)$fld["length"] . ") default '" . $this->db->escape( $fld["default"] ) . "' $after";

				}
				elseif ( $fld["type"] == "date" || $fld["type"] == "datetime" ) {
					$sql = "alter table `" . $this->table . "` add column `" . $fld["name"] .
						"` " . $fld["type"] . " default '" . $this->db->escape( $fld["default"] ) . "' $after";

				}
				elseif ( $fld["type"] == "timestamp" ) {
					$sql = "alter table `" . $this->table . "` add column `" . $fld["name"] . "` timestamp $after";

				}
				elseif ( $fld["type"] == "text" ) {
					$sql = "alter table `" . $this->table . "` add column `" . $fld["name"] . "` text $after";

				}
				elseif ( $fld["type"] == "bigint" ) {
					$sql = "alter table `" . $this->table . "` add column `" . $fld["name"] .
						"` bigint(" . (string)$fld["length"] . ") default '" . (int)$fld["default"] . "' $after";

				}
				elseif ( $fld["type"] == "int" ) {
					$sql = "alter table `" . $this->table . "` add column `" . $fld["name"] .
						"` int default '" . (int)$fld["default"] . "' $after";

				}
				elseif ( $fld["type"] == "double" ) {
					$sql = "alter table `" . $this->table . "` add column `" . $fld["name"] .
						"` double default '" . (int)$fld["default"] . "' $after";

				}
				elseif ( $fld["type"] == "float" ) {
					$sql = "alter table `" . $this->table . "` add column `" . $fld["name"] .
						"` float default '" . (int)$fld["default"] . "' $after";

				}
				elseif ( $fld["type"] == "tinyint" ) {
					$sql = "alter table `" . $this->table . "` add column `" . $fld["name"] . "` tinyint(1) default 0 $after";

				}
				elseif ( $fld["type"] == "blob" ) {
					$sql = "alter table `" . $this->table . "` add column `" . $fld["name"] . "` blob $after";

				}
				elseif ( $fld["type"] == "mediumblob" ) {
					$sql = "alter table `" . $this->table . "` add column `" . $fld["name"] . "` mediumblob $after";

				}
				else {
					die( "unknown field type dbCheck x> check -> " . $fld["type"] );

				}

				//~ die( "can't find " . $fld["name"] . ":$sql" );
				$this->db->Q( $sql );

			}

			$after = " after `" . $fld["name"] . "`";

		}

		foreach ( $this->indexs as $index ) {
			$res = $this->db->Result( sprintf( "SHOW INDEX FROM `%s` WHERE Key_name = '%s'", $this->table, $index['key'] ));
			$indexFound = FALSE;
			if ( $res->num_rows() > 0 ) {
				if ( $row = $res->fetch()) {
					\sys::logger( sprintf( "INDEX found `%s` => %s(%s)", $this->table, $index['key'], $row["Column_name"] ), 2 );
					$indexFound = TRUE;

				}

			}

			if ( !$indexFound ) {
				$sql = sprintf( "ALTER TABLE `%s` ADD INDEX `%s` (%s)",
					$this->db->escape( $this->table ),
					$this->db->escape( $index['key'] ),
					$this->db->escape( $index['field'] ) );
				\sys::logger( $sql, 2);
				$this->db->Q( $sql);
				\sys::logger( sprintf( "INDEX created `%s` => %s(%s)", $this->table, $index['key'], $index['field'] ), 2);

			}

		}

	}

}
