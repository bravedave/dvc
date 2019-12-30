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

class sys {
    static function logger( $v, $level = Logger::WARNING ) {

        $logdir = implode([
            \config::dataPath(),
            DIRECTORY_SEPARATOR,
            'logs'

        ]);

        if ( !is_dir( $logdir)) {
            mkdir( $logdir, 0777);
            chown( $logdir, 0777);

        }

        $logfile = implode([
            $logdir,
            DIRECTORY_SEPARATOR,
            date( 'Y-m-d'),
            '.log',

        ]);

        // create a log channel
        $log = new Logger('name');
        $log->pushHandler( new StreamHandler( $logfile, Logger::WARNING));

        if ( Logger::WARNING == $level) {
            $log->warning( $v);

        }
        else {
            $log->error( $v);


        }

    }

}
