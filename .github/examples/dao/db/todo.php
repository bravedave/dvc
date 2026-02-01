<?php
  // file: src/app/todo/dao/db/todo.php
  // MIT License

/**
 * note:
 *  id, autoincrement primary key is added to all tables - no need to specify
 *  field types are MySQL and are converted to SQLite equivalents as required
 */

$dbc = \sys::dbCheck('todo');

$dbc->defineField('created', 'datetime');
$dbc->defineField('updated', 'datetime');

$dbc->defineField('description', 'varchar');
$dbc->defineField('complete', 'varchar');

$dbc->check();  // actually do the work, check that table and fields exist