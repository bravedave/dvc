<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * Creates a min combined file for css
 * - requires a directory to write to
 * 		=> requires appdir/app/public/css/ to be writable
 *
 * then you can call one file in place of several,
 * if any css is updated, it will recompile it
 *
*/

namespace dvc;

use bravedave;

abstract class cssmin extends bravedave\dvc\cssmin {
}

logger::deprecated(sprintf('do not continue to use this class (dvc\cssmin) : %s', __METHOD__));
