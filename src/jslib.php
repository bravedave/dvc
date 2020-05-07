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

abstract class jslib extends dvc\jslib {}

\sys::logger( sprintf('deprecated : please call dvc\jslib directly : %s', __METHOD__));
