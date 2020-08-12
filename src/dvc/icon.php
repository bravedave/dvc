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
    const app = 10;
    const box = 20;

    const chat = 40;

    const chevronLeft = 41;
    const chevronRight = 42;

    const diamond = 45;
    const diamond_fill = 46;
    const diamond_half = 47;

    const fileRichText = 51;

    const github = 60;

    const house = 70;

    const question = 120;


    static function get( int $icon ) : string {
        if ( self::app == $icon) {
            return file_get_contents( __DIR__ . '/bootstrap4/icons/app.svg');

        }
        elseif ( self::box == $icon) {
            return file_get_contents( __DIR__ . '/bootstrap4/icons/box.svg');

        }
        elseif ( self::chat == $icon) {
            return file_get_contents( __DIR__ . '/bootstrap4/icons/chat.svg');

        }
        elseif ( self::chevronLeft == $icon) {
            return file_get_contents( __DIR__ . '/bootstrap4/icons/chevron-left.svg');

        }
        elseif ( self::chevronRight == $icon) {
            return file_get_contents( __DIR__ . '/bootstrap4/icons/chevron-right.svg');

        }
        elseif ( self::diamond == $icon) {
            return file_get_contents( __DIR__ . '/bootstrap4/icons/diamond.svg');

        }
        elseif ( self::diamond_fill == $icon) {
            return file_get_contents( __DIR__ . '/bootstrap4/icons/diamond-fill.svg');

        }
        elseif ( self::diamond_half == $icon) {
            return file_get_contents( __DIR__ . '/bootstrap4/icons/diamond-half.svg');

        }
        elseif ( self::fileRichText == $icon) {
            return file_get_contents( __DIR__ . '/bootstrap4/icons/file-richtext.svg');

        }
        elseif ( self::github == $icon) {
            return file_get_contents( __DIR__ . '/icons/github.svg');

        }
        elseif ( self::house == $icon) {
            return file_get_contents( __DIR__ . '/icons/house.svg');

        }
        elseif ( self::question == $icon) {
            return file_get_contents( __DIR__ . '/bootstrap4/icons/question.svg');

        }

        return file_get_contents( __DIR__ . '/bootstrap4/icons/question.svg');

    }

    static function inline( int $icon) {
        /*
        <style>
        .bi {
            display: inline-block;
            content: "";
            background-repeat: no-repeat;
            background-size: 1rem 1rem;
            background-position: 0px 1px;
            width: 1rem;
            height: 1rem;
        }

        .bi-github { background-image: url("<?= dvc\icon::inline( dvc\icon::github ) ?>"); }
        .bi-chevron-left { background-image: url("<?= dvc\icon::inline( dvc\icon::chevronLeft ) ?>"); }
        .bi-chevron-right { background-image: url("<?= dvc\icon::inline( dvc\icon::chevronRight ) ?>"); }

        </style>
         */
        $icon = self::get( $icon);

        $icon = \preg_replace( [
            "@#<@",
            "@^(\t|\s)*@m",
            "@(\r?\n|\r)@",
            '@"@',
        ], [
            '%23',
            '',
            '',
            "'"
        ], $icon);

        return 'data:image/svg+xml,'.$icon;

    }

}
