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
    
    // https://gist.github.com/braandl/f7965f62a5fecc379476d2c055838e36

    _.browser.isIPad = window.AuthenticatorAssertionResponse === undefined
        && window.AuthenticatorAttestationResponse === undefined
        && window.AuthenticatorResponse === undefined
        && window.Credential === undefined
        && window.CredentialsContainer === undefined
        && window.DeviceMotionEvent !== undefined
        && window.DeviceOrientationEvent !== undefined
        && navigator.maxTouchPoints === 5
        && navigator.plugins.length === 0
    	&& navigator.platform !== "iPhone";
        
	_.browser.isChromeOniOS = _.browser.isIPhone && ua.indexOf('CriOS') > -1;
	_.browser.isMobileDevice = _.browser.isIPhone || _.browser.isIPad || _.browser.isAndroid;
	_.browser.isPhone = _.browser.isIPhone || _.browser.isAndroid;

})(_brayworth_);
