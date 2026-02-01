<?php
  // file: src/app/contacts/dao/db/contacts.php
  // MIT License

/**
 * note:
 *  id, autoincrement primary key is added to all tables - no need to specify
 *  field types are MySQL and are converted to SQLite equivalents as required
 */

$dbc = \sys::dbCheck('contacts');

$dbc->defineField('created', 'datetime');
$dbc->defineField('updated', 'datetime');

$dbc->defineField('name', 'varchar');
$dbc->defineField('email', 'varchar');
$dbc->defineField('mobile', 'varchar');

$dbc->check();  // actually do the work, check that table and fields exist