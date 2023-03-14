<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace bravedave\dvc;

class dbCheck extends dao {
  public $temporary = false;

  protected $table;
  protected $pk = "id";
  protected $structure = [];
  protected $indexs = [];

  function __construct(db $db = null, $table, $pk = "id") {
    parent::__construct($db);

    $this->table = $table;
    $this->pk = $pk;
  }

  function defineField($name = "", $type = "varchar", $len = null, $dec = 0, $default = "") {
    if ($name == "")
      return (false);

    if ($type == "date" && $default == "")
      $default = "0000-00-00";
    if ($type == "datetime" && $default == "")
      $default = "0000-00-00 00:00:00";
    if (($type == "int" || $type == "bigint" || $type == "double" || $type == "float") && $default == "")
      $default = "0";

    if (is_null($len) || (int)$len < 1) {
      if (($type == "int")) {
        $len = 11;
      } elseif (($type == "varbinary")) {
        $len = 32;
      } elseif (($type == "bigint" || $type == "double" || $type == "decimal" || $type == "float")) {
        $len = 20;
      } else {
        $len = 45;  // probably varchar

      }
    }

    $this->structure[] = [
      "name" => $name,
      "type" => $type,
      "length" => $len,
      "decimal" => $dec,
      "default" => $default

    ];
  }

  function defineIndex($key, $field) {
    $this->indexs[] = [
      'key' => $key,
      'field' => $field

    ];
  }

  function check() {

    $debug = false;
    // $debug = true;

    $fields = [$this->pk . " BIGINT(20) NOT NULL AUTO_INCREMENT"];
    foreach ($this->structure as $f) {

      if ($f["type"] == "varchar") {

        // $fields[] = "`" . $f["name"] . "` varchar(" . (string)$f["length"] . ") default '" . $this->db->escape($f["default"]) . "'";
        $fields[] = sprintf('`%s` VARCHAR(%d) DEFAULT ', $f['name'], (int)$f['length'], $this->quote($f['default']));
      } elseif ($f["type"] == "date" || $f["type"] == "datetime") {

        $fields[] = "`" . $f["name"] . "` " . $f["type"] . " default '" . $this->db->escape($f["default"]) . "'";
      } elseif ($f["type"] == "timestamp") {
        $fields[] = "`" . $f["name"] . "` " . $f["type"];
      } elseif ($f["type"] == "text") {
        $fields[] = "`" . $f["name"] . "` text";
      } elseif ($f["type"] == "mediumtext") {
        $fields[] = "`" . $f["name"] . "` mediumtext";
      } elseif ($f["type"] == "longtext") {
        $fields[] = "`" . $f["name"] . "` longtext";
      } elseif ($f["type"] == "bigint") {
        $fields[] = "`" . $f["name"] . "` bigint(" . (string)$f["length"] . ") default '" . (int)$f["default"] . "'";
      } elseif ($f["type"] == "tinyint") {
        $fields[] = "`" . $f["name"] . "`  tinyint(1) default 0";
      } elseif ($f["type"] == "int") {
        $fields[] = "`" . $f["name"] . "`  int default '" . (int)$f["default"] . "'";
      } elseif ($f["type"] == "decimal") {

        $fields[] = sprintf('`%s` DECIMAL(%d,%d) DEFAULT %d', $f["name"], $f["length"], $f["decimal"], (int)$f["default"]);
      } elseif ($f["type"] == "double") {

        $fields[] = sprintf('`$s` DOUBLE DEFAULT %d', $f["name"], (int)$f["default"]);
      } elseif ($f["type"] == "float") {

        $fields[] = sprintf('`%s` FLOAT DEFAULT %d', $f["name"], (int)$f["default"]);
      } elseif ($f["type"] == "varbinary") {
        $fields[] = sprintf('`%s` varbinary(%s)', $f["name"], (string)$f["length"]);
      } elseif ($f["type"] == "blob") {
        $fields[] = "`" . $f["name"] . "`  blob";
      } elseif ($f["type"] == "mediumblob") {
        $fields[] = "`" . $f["name"] . "`  mediumblob";
      } elseif ($f["type"] == "longblob") {
        $fields[] = "`" . $f["name"] . "`  longblob";
      } else {

        die(sprintf('unknown field type dbCheck => check -> %s', $f['type']));
      }
    }

    $fields[] = sprintf('PRIMARY KEY  (`%s`)', $this->pk);
    foreach ($this->indexs as $key) {

      $fields[] = sprintf(' KEY `%s` (%s)', $key['key'], $this->quote($key['field']));
    }

    $sql = sprintf(
      'CREATE %s TABLE IF NOT EXISTS `%s`(%s)',
      $this->temporary ? 'TEMPORARY' : '',
      $this->table,
      implode(',', $fields)
    );
    //~ print "<pre>" . print_r( $fields, TRUE ) . "</pre>";
    //~ print $sql;
    $this->db->Q($sql);

    $fields = $this->db->fieldList($this->table);
    $fieldStructures = $this->db->fetchFields($this->table);
    $charset = $this->db->getCharSet();
    $after = "";
    foreach ($this->structure as $fld) {
      if (in_array($fld["name"], $fields)) {
        if ($fld["type"] == "varchar") {
          if (\config::$DB_ALTER_FIELD_STRUCTURES) {
            /*---[ we want to know if we should alter the field ]---*/
            // get structure for this field
            if ($charset = 'utf8') {
              foreach ($fieldStructures as $fieldStructure) {
                if ($fieldStructure->name == $fld["name"]) {
                  $fieldLength = ((int)$fieldStructure->length / 3);  // utf8 conversion
                  if ((int)$fieldLength < (int)$fld["length"]) {
                    //~ \sys::logger( sprintf( 'bingo baby :: %s : %s != %s', $fieldStructure->name, $fieldStructure->length, $fld["length"]));
                    $sql = sprintf(
                      'ALTER TABLE `%s` CHANGE COLUMN `%s` `%s` varchar(%s) default "%s"',
                      $this->table,
                      $fld["name"],
                      $fld["name"],
                      (string)$fld["length"],
                      $this->db->escape($fld["default"])
                    );

                    logger::info(sprintf('field length %s < %s', $fieldLength, ((int)$fld["length"])));
                    logger::sql($sql);;
                  }

                  break;
                }
              }
            }
            /*---[ end: we want to know if we should alter the field ]---*/
          }
        }
      } else {
        if ($fld["type"] == "varchar") {
          $sql = "alter table `" . $this->table . "` add column `" . $fld["name"] .
            "` varchar(" . (string)$fld["length"] . ") default '" . $this->db->escape($fld["default"]) . "' $after";
        } elseif ($fld["type"] == "date" || $fld["type"] == "datetime") {
          $sql = "alter table `" . $this->table . "` add column `" . $fld["name"] .
            "` " . $fld["type"] . " default '" . $this->db->escape($fld["default"]) . "' $after";
        } elseif ($fld["type"] == "timestamp") {
          $sql = "alter table `" . $this->table . "` add column `" . $fld["name"] . "` timestamp $after";
        } elseif ($fld["type"] == "text") {
          $sql = "alter table `" . $this->table . "` add column `" . $fld["name"] . "` text $after";
        } elseif ($fld["type"] == "mediumtext") {
          $sql = "alter table `" . $this->table . "` add column `" . $fld["name"] . "` mediumtext $after";
        } elseif ($fld["type"] == "longtext") {
          $sql = "alter table `" . $this->table . "` add column `" . $fld["name"] . "` longtext $after";
        } elseif ($fld["type"] == "bigint") {
          $sql = "alter table `" . $this->table . "` add column `" . $fld["name"] .
            "` bigint(" . (string)$fld["length"] . ") default '" . (int)$fld["default"] . "' $after";
        } elseif ($fld["type"] == "int") {

          $sql = sprintf(
            'alter table `%s` add column `%s` int default %s%s',
            $this->table,
            $this->escape($fld["name"]),
            $this->quote((int)$fld["default"]),
            $after
          );
        } elseif ($fld["type"] == "decimal") {

          $sql = sprintf(
            'alter table `%s` add column `%s` decimal(%d,%d) default %d%s',
            $this->table,
            $fld["name"],
            $fld["length"],
            $fld["decimal"],
            (int)$fld["default"],
            $after
          );
        } elseif ($fld["type"] == "double") {
          $sql = "alter table `" . $this->table . "` add column `" . $fld["name"] .
            "` double default '" . (int)$fld["default"] . "' $after";
        } elseif ($fld["type"] == "float") {
          $sql = "alter table `" . $this->table . "` add column `" . $fld["name"] .
            "` float default '" . (int)$fld["default"] . "' $after";
        } elseif ($fld["type"] == "varbinary") {
          $sql = sprintf(
            'alter table `%s` add column `%s` varbinary(%s) %s',
            $this->table,
            $fld["name"],
            (string)$fld["length"],
            $after
          );
        } elseif ($fld["type"] == "tinyint") {
          $sql = "alter table `" . $this->table . "` add column `" . $fld["name"] . "` tinyint(1) default 0 $after";
        } elseif ($fld["type"] == "blob") {
          $sql = "alter table `" . $this->table . "` add column `" . $fld["name"] . "` blob $after";
        } elseif ($fld["type"] == "mediumblob") {
          $sql = "alter table `" . $this->table . "` add column `" . $fld["name"] . "` mediumblob $after";
        } elseif ($fld["type"] == "longblob") {
          $sql = "alter table `" . $this->table . "` add column `" . $fld["name"] . "` longblob $after";
        } else {
          die("unknown field type dbCheck x> check -> " . $fld["type"]);
        }

        //~ die( "can't find " . $fld["name"] . ":$sql" );
        $this->db->Q($sql);
      }

      $after = " after `" . $fld["name"] . "`";
    }

    foreach ($this->indexs as $index) {

      $res = $this->db->Result(sprintf("SHOW INDEX FROM `%s` WHERE Key_name = '%s'", $this->table, $index['key']));
      $indexFound = FALSE;
      if ($res->num_rows() > 0) {

        if ($row = $res->fetch()) {

          // logger::info(sprintf("INDEX found `%s` => %s(%s)", $this->table, $index['key'], $row["Column_name"]), 2);
          $indexFound = TRUE;
        }
      }

      if (!$indexFound) {

        $sql = sprintf(
          "ALTER TABLE `%s` ADD INDEX `%s` (%s)",
          $this->escape($this->table),
          $this->escape($index['key']),
          $this->escape($index['field'])
        );

        if ($debug) logger::sql($sql);
        $this->Q($sql);
        if ($debug) logger::debug(sprintf("INDEX created `%s` => %s(%s)", $this->table, $index['key'], $index['field']), 2);
      }
    }

    return true;
  }
}
