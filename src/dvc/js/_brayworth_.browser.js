/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */
/*jshint esversion: 6 */
( _ => {
	_.browser = {
		isAndroid : navigator.userAgent.toLowerCase().indexOf('android') > -1,
		isIPhone : navigator.userAgent.toLowerCase().indexOf('iphone') > -1,
		isIPad : navigator.userAgent.toLowerCase().indexOf('ipad') > -1,
		isFirefox : navigator.userAgent.toLowerCase().indexOf('firefox') > -1

	};

	_.browser.isChromeOniOS = _.browser.isIPhone && navigator.userAgent.toLowerCase().indexOf('CriOS') > -1;
	_.browser.isMobileDevice = _.browser.isIPhone || _.browser.isIPad || _.browser.isAndroid;
	_.browser.isPhone = _.browser.isIPhone || _.browser.isAndroid;

})(_brayworth_);
