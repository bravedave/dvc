<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * This work is licensed under a Creative Commons Attribution 4.0 International Public License.
 *      http://creativecommons.org/licenses/by/4.0/
 *
*/

namespace dvc\core;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class loghandler {
    protected static $_loghandler = null;

    static function logger( $v, $level = Logger::WARNING ) {
        if ( \is_null( self::$_loghandler)) {
            $logfile = implode([
                \config::logPath(),
                DIRECTORY_SEPARATOR,
                date( 'Y-m-d'),
                '.log',

            ]);

            // create a log channel
            self::$_loghandler = new Logger('dvc');
            self::$_loghandler->pushHandler( new StreamHandler( $logfile, Logger::WARNING));

        }


        if ( Logger::INFO == $level) {
            self::$_loghandler->info( $v);

        }
        elseif ( Logger::WARNING == $level) {
            self::$_loghandler->warning( $v);

        }
        else {
            self::$_loghandler->error( $v);

        }

    }

}
