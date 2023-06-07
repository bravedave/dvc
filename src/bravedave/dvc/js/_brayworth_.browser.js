/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */
( _ => {
	let ua = navigator.userAgent.toLowerCase();
	_.browser = {
		isAndroid : ua.indexOf('android') > -1,
		isIPhone : ua.indexOf('iphone') > -1,
		isIPad : ua.indexOf('ipad') > -1,
		isFirefox : ua.indexOf('firefox') > -1

	};
    
    // https://stackoverflow.com/questions/57765958/how-to-detect-ipad-and-ipad-os-version-in-ios-13-and-up/57924983#57924983

    _.browser.isIPad = (navigator.userAgent.match(/(iPad)/) /* iOS pre 13 */ ||
            (navigator.platform === 'MacIntel' && navigator.maxTouchPoints >
            1) /* iPad OS 13 */ );
        
	_.browser.isChromeOniOS = _.browser.isIPhone && ua.indexOf('CriOS') > -1;
	_.browser.isMobileDevice = _.browser.isIPhone || _.browser.isIPad || _.browser.isAndroid;
	_.browser.isPhone = _.browser.isIPhone || _.browser.isAndroid;

})(_brayworth_);
