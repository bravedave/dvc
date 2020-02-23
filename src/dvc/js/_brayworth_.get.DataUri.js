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
_brayworth_.get.DataUri = function( url) {
	return new Promise( function( resolve, reject) {
		//~ console.log( '_cms_.get.DataUri : converting : ', url);
		let img = new Image();

		img.onload = function () {
			let canvas = document.createElement('canvas');
			canvas.width = this.naturalWidth; // or 'width' if you want a special/scaled size
			canvas.height = this.naturalHeight; // or 'height' if you want a special/scaled size

			canvas.getContext('2d').drawImage(this, 0, 0);

			// Get raw image data
			resolve( canvas.toDataURL('image/jpeg').replace(/^data:image\/(png|jpg|jpeg);base64,/, ''));
			//~ console.log( 'done convert');

		};

		img.src = url;

	});

};
