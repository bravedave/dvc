<?php
  // file: src/app/contacts/config.php
  // MIT License

namespace contacts;

use config as rootConfig;

class config extends rootConfig {

  const contacts_db_version = 1;

  const label = 'Contacts';
  const label_view = 'View Contact';
  const label_edit = 'Edit Contact';

  static function contacts_checkdatabase() {

    $dao = new dao\dbinfo;
    $dao->checkVersion('contacts', self::contacts_db_version);
  }
}