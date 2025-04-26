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

use config;

class dbCheck extends dao {
  public $temporary = false;

  protected $table;
  protected $pk = 'id';
  protected $structure = [];
  protected $indexs = [];

  function __construct(db|null $db = null, $table, $pk = 'id') {
    parent::__construct($db);

    $this->table = $table;
    $this->pk = $pk;
  }

  function defineField($name = '', $type = 'varchar', $len = null, $dec = 0, $default = '') {

    if ($name == '') return false;

    if ($type == 'date' && $default == '') $default = '0000-00-00';
    if ($type == 'datetime' && $default == '') $default = '0000-00-00 00:00:00';
    if (($type == 'int' || $type == 'bigint' || $type == 'double' || $type == 'float') && $default == '')
      $default = '0';

    if (is_null($len) || (int)$len < 1) {

      if (($type == 'int')) {

        $len = 11;
      } elseif (($type == 'varbinary')) {

        $len = 32;
      } elseif (($type == 'bigint' || $type == 'double' || $type == 'decimal' || $type == 'float')) {

        $len = 20;
      } else {

        $len = 45;  // probably varchar
      }
    }

    $this->structure[] = [
      'name' => $name,
      'type' => $type,
      'length' => $len,
      'decimal' => $dec,
      'default' => $default
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

    $fields = [$this->pk . ' BIGINT(20) NOT NULL AUTO_INCREMENT'];
    foreach ($this->structure as $f) {

      if ($f['type'] == 'varchar') {

        $fields[] = sprintf('`%s` VARCHAR(%d) DEFAULT %s', $f['name'], (int)$f['length'], $this->quote($f['default']));
      } elseif ($f['type'] == 'date' || $f['type'] == 'datetime') {

        $fields[] = sprintf('`%s` %s DEFAULT %s', $f['name'], strtoupper($f['type']), $this->quote($f['default']));
      } elseif ($f['type'] == 'timestamp') {

        $fields[] = sprintf('`%s` TIMESTAMP', $f['name']);
      } elseif ($f['type'] == 'text') {

        $fields[] = sprintf('`%s` TEXT', $f['name']);
      } elseif ($f['type'] == 'mediumtext') {

        $fields[] = sprintf('`%s` MEDIUMTEXT', $f['name']);
      } elseif ($f['type'] == 'longtext') {

        $fields[] = sprintf('`%s` LONGTEXT', $f['name']);
      } elseif ($f['type'] == 'bigint') {

        $fields[] = sprintf('`%s` BIGINT(%d) DEFAULT %d', $f['name'], (int)$f['length'], (int)$f['default']);
      } elseif ($f['type'] == 'tinyint') {

        $fields[] = sprintf('`%s` TINYINT(1) DEFAULT 0', $f['name']);
      } elseif ($f['type'] == 'int') {

        $fields[] = sprintf('`%s` INT DEFAULT %d', $f['name'], (int)$f['default']);
      } elseif ($f['type'] == 'decimal') {

        $fields[] = sprintf('`%s` DECIMAL(%d,%d) DEFAULT %d', $f['name'], $f['length'], $f['decimal'], (int)$f['default']);
      } elseif ($f['type'] == 'double') {

        $fields[] = sprintf('`%s` DOUBLE DEFAULT %d', $f['name'], (int)$f['default']);
      } elseif ($f['type'] == 'float') {

        $fields[] = sprintf('`%s` FLOAT DEFAULT %d', $f['name'], (int)$f['default']);
      } elseif ($f['type'] == 'varbinary') {

        $fields[] = sprintf('`%s` varbinary(%s)', $f['name'], (string)$f['length']);
      } elseif ($f['type'] == 'blob') {

        $fields[] = sprintf('`%s` BLOB', $f['name']);
      } elseif ($f['type'] == 'mediumblob') {

        $fields[] = sprintf('`%s` MEDIUMBLOB', $f['name']);
      } elseif ($f['type'] == 'longblob') {

        $fields[] = sprintf('`%s` LONGBLOB', $f['name']);
      } else {

        die(sprintf('unknown field type dbCheck => check -> %s', $f['type']));
      }
    }

    $fields[] = sprintf('PRIMARY KEY  (`%s`)', $this->pk);
    foreach ($this->indexs as $key) {

      $fields[] = sprintf(' KEY `%s` (%s)', $key['key'], $key['field']);
    }

    $sql = sprintf(
      'CREATE %s TABLE IF NOT EXISTS `%s`(%s)',
      $this->temporary ? 'TEMPORARY' : '',
      $this->table,
      implode(',', $fields)
    );
    $this->Q($sql);

    $fields = $this->db->fieldList($this->table);
    $fieldStructures = $this->db->fetchFields($this->table);
    $charset = $this->db->getCharSet();
    $after = '';
    foreach ($this->structure as $fld) {

      if (in_array($fld['name'], $fields)) {

        if ($fld['type'] == 'varchar') {

          if (config::$DB_ALTER_FIELD_STRUCTURES) {

            /*---[ we want to know if we should alter the field ]---*/
            // get structure for this field
            if ('utf8' == $charset) {

              foreach ($fieldStructures as $fieldStructure) {

                if ($fieldStructure->name == $fld['name']) {

                  $fieldLength = ((int)$fieldStructure->length / 3);  // utf8 conversion
                  if ((int)$fieldLength < (int)$fld['length']) {

                    //~ \sys::logger( sprintf( 'bingo baby :: %s : %s != %s', $fieldStructure->name, $fieldStructure->length, $fld['length']));
                    $sql = sprintf(
                      'ALTER TABLE `%s` CHANGE COLUMN `%s` `%s` VARCHAR(%s) DEFAULT %s',
                      $this->table,
                      $fld['name'],
                      $fld['name'],
                      (string)$fld['length'],
                      $this->quote($fld['default'])
                    );

                    logger::info(sprintf('field length %s < %s', $fieldLength, ((int)$fld['length'])));
                    logger::sql($sql);
                  }

                  break;
                }
              }
            }
            /*---[ end: we want to know if we should alter the field ]---*/
          }
        }
      } else {

        if ($fld['type'] == 'varchar') {

          $sql = sprintf(
            'ALTER TABLE `%s` ADD COLUMN `%s` varchar(%s) DEFAULT %s %s',
            $this->table,
            $this->escape($fld['name']),
            (string)$fld['length'],
            $this->quote($fld['default']),
            $after
          );
        } elseif ($fld['type'] == 'date' || $fld['type'] == 'datetime') {

          $sql = sprintf(
            'ALTER TABLE `%s` ADD COLUMN `%s` %s DEFAULT %s %s',
            $this->table,
            $this->escape($fld['name']),
            $fld['type'],
            $this->quote($fld['default']),
            $after
          );
        } elseif ($fld['type'] == 'timestamp') {

          $sql = sprintf(
            'ALTER TABLE `%s` ADD COLUMN `%s` TIMESTAMP %s',
            $this->table,
            $this->escape($fld['name']),
            $after
          );
        } elseif ($fld['type'] == 'text') {

          $sql = sprintf(
            'ALTER TABLE `%s` ADD COLUMN `%s` TEXT %s',
            $this->table,
            $this->escape($fld['name']),
            $after
          );
        } elseif ($fld['type'] == 'mediumtext') {

          $sql = sprintf(
            'ALTER TABLE `%s` ADD COLUMN `%s` MEDIUMTEXT %s',
            $this->table,
            $this->escape($fld['name']),
            $after
          );
        } elseif ($fld['type'] == 'longtext') {

          $sql = sprintf(
            'ALTER TABLE `%s` ADD COLUMN `%s` LONGTEXT %s',
            $this->table,
            $this->escape($fld['name']),
            $after
          );
        } elseif ($fld['type'] == 'bigint') {

          $sql = sprintf(
            'ALTER TABLE `%s` ADD COLUMN `%s` bigint(%s) DEFAULT %s %s',
            $this->table,
            $this->escape($fld['name']),
            (string)$fld['length'],
            (int)$fld['default'],
            $after
          );
        } elseif ($fld['type'] == 'int') {

          $sql = sprintf(
            'ALTER TABLE `%s` ADD COLUMN `%s` INT DEFAULT %s %s',
            $this->table,
            $this->escape($fld['name']),
            $this->quote((int)$fld['default']),
            $after
          );
        } elseif ($fld['type'] == 'decimal') {

          $sql = sprintf(
            'ALTER TABLE `%s` ADD COLUMN `%s` decimal(%d,%d) DEFAULT %d %s',
            $this->table,
            $fld['name'],
            $fld['length'],
            $fld['decimal'],
            (int)$fld['default'],
            $after
          );
        } elseif ($fld['type'] == 'double') {

          $sql = sprintf(
            'ALTER TABLE `%s` ADD COLUMN `%s` DOUBLE DEFAULT %s %s',
            $this->table,
            $this->escape($fld['name']),
            $this->quote((int)$fld['default']),
            $after
          );
        } elseif ($fld['type'] == 'float') {

          $sql = sprintf(
            'ALTER TABLE `%s` ADD COLUMN `%s` FLOAT DEFAULT %s %s',
            $this->table,
            $this->escape($fld['name']),
            $this->quote((int)$fld['default']),
            $after
          );
        } elseif ($fld['type'] == 'varbinary') {

          $sql = sprintf(
            'ALTER TABLE `%s` ADD COLUMN `%s` VARBINARY(%s) %s',
            $this->table,
            $this->escape($fld['name']),
            (string)$fld['length'],
            $after
          );
        } elseif ($fld['type'] == 'tinyint') {

          $sql = sprintf(
            'ALTER TABLE `%s` ADD COLUMN `%s` TINYINT(1) DEFAULT 0 %s',
            $this->table,
            $this->escape($fld['name']),
            $after
          );
        } elseif ($fld['type'] == 'blob') {

          $sql = sprintf(
            'ALTER TABLE `%s` ADD COLUMN `%s` BLOB %s',
            $this->table,
            $this->escape($fld['name']),
            $after
          );
        } elseif ($fld['type'] == 'mediumblob') {

          $sql = sprintf(
            'ALTER TABLE `%s` ADD COLUMN `%s` MEDIUMBLOB %s',
            $this->table,
            $this->escape($fld['name']),
            $after
          );
        } elseif ($fld['type'] == 'longblob') {

          $sql = sprintf(
            'ALTER TABLE `%s` ADD COLUMN `%s` LONGBLOB %s',
            $this->table,
            $this->escape($fld['name']),
            $after
          );
        } else {

          die('unknown field type dbCheck x> check -> ' . $fld['type']);
        }

        //~ die( 'can't find ' . $fld['name'] . ':$sql' );
        $this->Q($sql);
      }

      $after = sprintf(' AFTER `%s`', $this->escape($fld['name']));
    }

    foreach ($this->indexs as $index) {

      $res = $this->Result(sprintf('SHOW INDEX FROM `%s` WHERE Key_name = %s', $this->table, $this->quote($index['key'])));
      $indexFound = FALSE;
      if ($res->num_rows() > 0) {

        if ($row = $res->fetch()) {

          // logger::info(sprintf('INDEX found `%s` => %s(%s)', $this->table, $index['key'], $row['Column_name']), 2);
          $indexFound = TRUE;
        }
      }

      if (!$indexFound) {

        $sql = sprintf(
          'ALTER TABLE `%s` ADD INDEX `%s` (%s)',
          $this->escape($this->table),
          $this->escape($index['key']),
          $this->escape($index['field'])
        );

        if ($debug) logger::sql($sql);
        $this->Q($sql);
        if ($debug) logger::debug(sprintf('INDEX created `%s` => %s(%s)', $this->table, $index['key'], $index['field']), 2);
      }
    }

    return true;
  }
}
