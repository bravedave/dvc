<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

$dbc = sys::dbCheck('todo');

$dbc->defineField('description', 'text');

$dbc->check();
