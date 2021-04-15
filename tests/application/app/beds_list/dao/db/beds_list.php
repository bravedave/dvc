<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dao;

$dbc = \sys::dbCheck( 'beds_list');

$dbc->defineField( 'beds', 'varchar', 10 );
$dbc->defineField( 'description', 'varchar');

$dbc->check();
