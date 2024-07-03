/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */
(_ => {

  _.ScrollTo = function (el, params) {

    const options = {
      ...{
        marginTop: 0,
        duration: 800
      }, ...params
    };

    return (new Promise((resolve, reject) => {

      let _el = (el instanceof jQuery ? el : $(el));
      if (_el.length < 1) {

        console.log('element not found', el);
        resolve();
        return;
      }

      let t = _el.offset().top;
      // console.log( _el, t);

      const parent = _el.closest('.modal');
      if (parent.length == 0) {

        const nav = $('body>nav');
        if (nav.length > 0) {

          t -= nav.outerHeight();
        } else {

          const hdr = $('body>header');
          if (hdr.length > 0) t -= (hdr.outerHeight());
        }
      }

      t -= options.marginTop;
      t = Math.max(20, t);

      if (parent.length > 0) {

        parent.animate({ scrollTop: t }, {
          duration: options.duration,
          complete: resolve,
          fail: reject,
        });
      } else {

        _el[0].scrollIntoView({ behavior: "smooth" });
        resolve();
      }
    }));
  };

  _.hashScroll = () => {

    /** Scrolls the content into view **/
    $('a[href*="#"]:not([href="#"] , .carousel-control, .ui-tabs-anchor)').on('click', function () {

      if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {

        let target = $(this.hash);
        target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
        if (target.length) {

          if (/nav/i.test(target.prop('tagName'))) return;

          _.ScrollTo(target);
          return false;
        }
      }
    });
  };
})(_brayworth_);
