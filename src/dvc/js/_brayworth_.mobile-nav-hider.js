/*
	David Bray
	D'Arcy Estate Agents & BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Attribution-NonCommercial-NoDerivatives 4.0 International Public License.
		https://creativecommons.org/licenses/by-nc-nd/4.0/

	*/
$(document).ready( () => {
	if ( !_brayworth_.browser.isMobileDevice) return;

	let lastScrollTop = 0;
	let downScroll = 0;
	let upScroll = 0;
	let delta = 50;
	let hasClass = false;

	window.addEventListener("scroll", (e) => {
		let st = document.documentElement.scrollTop;

		//~ console.log('scroll', st, lastScrollTop);
		if ( st > lastScrollTop) {
			// upscroll code
			upScroll += ( st - lastScrollTop);
			if ( upScroll > delta) {
				downScroll = 0;
				//~ console.log( 'up', upScroll);
				$('body').addClass( 'upscroll');
				hasClass = true;

			}

		}
		else {
			// downscroll code
			downScroll += ( lastScrollTop - st);
			if ( downScroll > delta) {
				upScroll = 0;
				$('body').removeClass( 'upscroll');
				hasClass = false;

			}
			else if ( st == 0 && hasClass) {
				$('body').removeClass( 'upscroll');
				hasClass = false;

			}

		}

		lastScrollTop = st <= 0 ? 0 : st; // For Mobile or negative scrolling

	}, true);

	//~ console.log('scroll-set');

});
