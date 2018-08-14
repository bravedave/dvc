/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

*/

if ( typeof _brayworth_ == 'undefined')
	var _brayworth_ = {};

_brayworth_.browser = {}

_brayworth_.browser.isIPhone = navigator.userAgent.toLowerCase().indexOf('iphone') > -1;
_brayworth_.browser.isIPad = navigator.userAgent.toLowerCase().indexOf('ipad') > -1;
_brayworth_.browser.isChromeOniOS = _brayworth_.browser.isIPhone && navigator.userAgent.toLowerCase().indexOf('CriOS') > -1;
_brayworth_.browser.isMobileDevice = _brayworth_.browser.isIPhone || _brayworth_.browser.isIPad;

