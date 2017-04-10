<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	DO NOT change this file
	Copy it to <application>/app and modify it there

	Note: Since core components use config::variables,
		it would be risky (as in they may not be found here)
		to store configuration variables in this file
		- they should be stored in dvc\config (where
		they will be more easily found)
	*/
abstract class config extends dvc\config {}

