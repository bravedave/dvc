<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/

NameSpace Exceptions;

class FileNotSpecified extends Exception {
	protected $_text = 'the file or path was not specified when calling the function';

}
