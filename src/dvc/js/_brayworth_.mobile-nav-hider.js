/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */

$(document).ready(() => {
  if (!_brayworth_.browser.isMobileDevice) return;

  let lastScrollTop = 0;
  let downScroll = 0;
  let upScroll = 0;
  let delta = 50;
  let hasClass = false;

  const body = $('body');

  window.addEventListener("scroll", (e) => {
    let st = document.documentElement.scrollTop;

    if (st > lastScrollTop) {

      // upscroll code
      upScroll += (st - lastScrollTop);
      if (upScroll > delta) {

        downScroll = 0;

        if (!body.find('nav > .navbar-collapse').hasClass('show')) {

          body.addClass('upscroll');
          hasClass = true;
        }
      }
    }
    else {

      // downscroll code
      downScroll += (lastScrollTop - st);
      if (downScroll > delta) {

        upScroll = 0;
        body.removeClass('upscroll');
        hasClass = false;
      }
      else if (st == 0 && hasClass) {

        body.removeClass('upscroll');
        hasClass = false;
      }
    }

    lastScrollTop = st <= 0 ? 0 : st; // For Mobile or negative scrolling
  }, true);
});
