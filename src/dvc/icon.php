<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace dvc;

class icon {
    const github = 1;
    const house = 2;

    const question = 22;


    static function get( int $icon ) : string {
        if ( self::github == $icon) {
            return file_get_contents( __DIR__ . '/icons/github.svg');

        }
        elseif ( self::house == $icon) {
            return file_get_contents( __DIR__ . '/icons/house.svg');

        }
        elseif ( self::question == $icon) {

        }

        return file_get_contents( __DIR__ . '/icons/question.svg');

    }

}
