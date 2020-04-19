<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

$path = realpath( implode( DIRECTORY_SEPARATOR, [
    __DIR__,
    '..',
    '..',
    '..',
    '..',
    'CHANGELOG.md'
 ]));

if ( file_exists( $path)) {
    $fc = file_get_contents( $path);
    print \Parsedown::instance()->text( $fc);


}
