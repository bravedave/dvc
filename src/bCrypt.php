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

class bCrypt extends dvc\bCrypt {}

\sys::logger( sprintf('deprecated : do not continue to use this class (bCrypt) : %s', __FILE__));
