/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

*/
_brayworth_.browser = (function() {
	let j = {
		isIPhone : navigator.userAgent.toLowerCase().indexOf('iphone') > -1,
		isIPad : navigator.userAgent.toLowerCase().indexOf('ipad') > -1,

	}
	j.isChromeOniOS : j.isIPhone && navigator.userAgent.toLowerCase().indexOf('CriOS') > -1;
	j.isMobileDevice : j.isIPhone || j.isIPad;

	return j;

})();
