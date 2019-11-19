/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * This work is licensed under a Creative Commons Attribution 4.0 International Public License.
 *      http://creativecommons.org/licenses/by/4.0/
 *
*/
/*jshint esversion: 6 */
_brayworth_.get = function( url) {
	return new Promise(function(resolve, reject) {
		let req = new XMLHttpRequest();
		req.open('GET', url);

		req.onload = function() {
			// This is called even on 404 etc so check the status
			if (req.status == 200) {
				resolve( req.response);	// Resolve the promise with the response text
			}
			else {
				reject( Error( req.statusText)); // Otherwise reject with the status text which will hopefully be a meaningful error

			}

		};

		req.onerror = function() { reject( Error("Network Error")); };	// Handle network errors
		req.send();		// Make the request

	});

};
