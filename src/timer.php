<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 *  DO NOT change this file
 *  Copy it to <application>/app/dvc/ and modify it there
*/

class timer extends dvc\timer {}

\sys::trace(sprintf('deprecated : please call dvc\timer directly : %s', __METHOD__));
