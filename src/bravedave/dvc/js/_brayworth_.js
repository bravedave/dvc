/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */
if (!window._brayworth_) {
  window._brayworth_ = srch => {
    let _ = _brayworth_;
    /**
     * this is jQuery like .. ish, working on it
     */
    if (!!srch) {
      if (/^#[a-z][a-z0-9_\-]*$/i.test(String(srch))) {
        srch = String(srch).substring(1);

        if (_.debug) console.log('document.getElementById(', srch, ')');

        return document.getElementById(srch);
      }
      else if (/^\.[a-z]/.test(String(srch))) {
        srch = String(srch).substring(1);

        if (_.debug) console.log('document.getElementsByClassName(', srch, ')');

        let col = document.getElementsByClassName(srch);
        let r = [];
        for (let i = 0; i < col.length; i++) {
          r.push(col[i]);
        }
        return r;
      }
      else {

        if (_.debug) console.log('document.querySelectorAll(', srch, ')');

        return document.querySelectorAll(srch);
      }
    }

    return (_);

  };
}

(_ => {
  _.version = 0.2;
  _._brayworth_ = true;
  _.currentUser = false;
  _.debug = false;
  _.logon_retrieve_password = false;
  _.templates = {};

  _.asLocaleDate = ansi => {

    let d = _.dayjs(ansi);
    if (d.isValid() && d.unix() > 0) return d.format('L');
    return '';
  };

  _.asLocaleNumber = (n, fractions = 2) => {

    if (n == 0) n = 0;  // -0 also == 0, but prints -0

    return Number(n).toLocaleString(undefined, {
      minimumFractionDigits: fractions,
      maximumFractionDigits: fractions
    });
  };

  // https://stackoverflow.com/questions/18749591/encode-html-entities-in-javascript
  _.encodeHTMLEntities = s => s.replace(/[\u00A0-\u9999<>\&"]/g, i => '&#' + i.charCodeAt(0) + ';');

  _.hideContexts = e => {

    if (!!e) {

      e.stopPropagation();
      // e.preventDefault();
    }

    $(document).trigger('hide-contexts');
  };

  _.bootstrap = {

    v4: {
      v5: html => {

        html = String(html).replace(/data-dismiss/g, 'data-bs-dismiss');
        html = String(html).replace(/data-toggle/g, 'data-bs-toggle');

        let o = $(html);

        o.find('.close')
          .addClass('btn-close')
          .removeClass('close')
          .html('');

        o.find('.btn-close').parent().attr('data-bs-theme', 'dark')

        o.find('.input-group-text').each((i, el) => {

          let _el = $(el);
          let parent = _el.parent();

          if (!parent.hasClass('input-group')) {

            _el.unwrap();
            parent.remove();
          }
        });

        o.find('select.custom-select')
          .removeClass('custom-select').addClass('form-select');
        o.find('.text-left').removeClass('text-left').addClass('text-start');
        o.find('.text-right').removeClass('text-right').addClass('text-end');
        o.find('.ml-auto').removeClass('ml-auto').addClass('ms-auto');
        o.find('.mr-auto').removeClass('mr-auto').addClass('me-auto');

        let s = o[0].outerHTML;

        return s;
      }
    },
    v5: {
      v4: html => {

        html = String(html).replace(/data-bs-dismiss/g, 'data-dismiss');
        html = String(html).replace(/data-bs-toggle/g, 'data-toggle');

        let o = $(html);

        // o.find('.btn-close')
        //   .addClass('close')
        //   .removeClass('btn-close')
        //   .html('');

        o.find('.btn-close').parent().removeAttr('data-bs-theme')

        o.find('.input-group-text').each((i, el) => {

          let _el = $(el);
          let parent = _el.parent();

          if (!(parent.hasClass('input-group-prepend') || parent.hasClass('input-group-append'))) {

            _el.wrap('<div class="input-group-append"></div>');
          }
        });

        o.find('select.form-select')
          .removeClass('form-select').addClass('custom-select');
        o.find('.text-start').removeClass('text-start').addClass('text-left');
        o.find('.text-end').removeClass('text-end').addClass('text-right');
        o.find('.ms-auto').removeClass('ms-auto').addClass('ml-auto');
        o.find('.me-auto').removeClass('me-auto').addClass('mr-auto');

        let s = o[0].outerHTML;

        return s;
      }
    },
    version: () => {

      if (undefined != window.bootstrap) {

        if (!!bootstrap.Alert) {

          if (/^5/.test(bootstrap.Alert.VERSION)) return 5;
          if (/^4/.test(bootstrap.Alert.VERSION)) return 4;
          if (/3/.test(bootstrap.Alert.VERSION)) return 3;
        }
      }

      return 0;
    }
  };

  _.bootstrap_version = _.bootstrap.version;
  _.bootstrap_version.extended = () => {

    if (undefined != window.bootstrap) {

      if (!!bootstrap.Alert) return bootstrap.Alert.VERSION;
    }

    return 0;
  };

  _.darkMode = () => window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
  _.darkMode.matchTheme = () => { if (_.darkMode()) document.documentElement.dataset.bsTheme = 'dark'; };

  _.REMtoPX = i => {
    let rem = parseFloat(getComputedStyle($(':root')[0]).fontSize);
    return (rem * i);

  };

  _.desktop = {
    width: 64,
    height: 32,
    isSmall: () => {
      if ($(window).width() < _.REMtoPX(_.desktop.width)) return true;
      if ($(window).height() < _.REMtoPX(_.desktop.height)) return true;

      return false;

    }

  };

  _.mobileErrorHandler = (a, b, c, d, e) => {
    _.growlError(`msg(${c}): ${a}`);

    // console.log(`message: ${a}`);
    // console.log(`source: ${b}`);
    // console.log(`lineno: ${c}`);
    // console.log(`colno: ${d}`);
    // console.log(`error: ${e}`);

    // return true;
  };

  _.ready = fn => {

    switch (document.readyState) {

      case "loading":
        // console.log('document ready state : ', document.readyState);
        document.addEventListener(
          'DOMContentLoaded',
          () => fn(_brayworth_),
          { once: true }
        );
        break;

      default:
        fn(_brayworth_);
        break
    }
  };

  _.timezone = '';

  _.table = {};

  $(document).on('content-load', (e, j) => {
    const els = [
      'header',
      'nav',
      'main',
      'footer'
    ];

    $.each(j, (k, v) => {

      if (els.indexOf(k) > -1) {

        fetch(v)
          .then(response => response.text())
          .then(html => {

            let _k = $('body >' + k);

            _k.html(html);
            $('a[wapp-role="navigation"]', _k).each((i, el) => {
              $(el).on('click', function (e) {
                e.stopPropagation(); e.preventDefault();

                let _me = $(this);
                let _data = _me.data();
                // console.log( _data);

                let o = {};
                o[_data.target] = _data.url;
                // console.log( o);

                $(document).trigger('content-load', o);
              });
            });
          });
      }
    });
  });

  /**
   * https://stackoverflow.com/questions/6139225/how-to-detect-a-long-touch-pressure-with-javascript-for-android-and-iphone
   */
  _.longTouchDetector_timeout = 1200;

  _.longTouchDetector = (element, evt) => {

    element
      .css({
        '-webkit-user-select': 'none'
      })
      .on('touchstart', function (e) {

        if (!!this.dataset.pressTimer) {

          // Cancel the timeout if the mouseup event fires.
          clearTimeout(this.dataset.pressTimer);
          delete this.dataset.pressTimer;
        }

        // Start counting when the mousedown event fires.
        this.dataset.pressTimer =
          setTimeout(() => evt.call(this, e), _.longTouchDetector_timeout);
        _.hideContexts();
      })
      .on('touchend', function (e) {

        if (!!this.dataset.pressTimer) {

          // Cancel the timeout if the mouseup event fires.
          clearTimeout(this.dataset.pressTimer);
          delete this.dataset.pressTimer;
        }
      });
  };

  _.ready(() => {
    dayjs.extend(dayjs_plugin_localeData);
    dayjs.extend(dayjs_plugin_localizedFormat);
    dayjs.extend(dayjs_plugin_utc);
    dayjs.extend(dayjs_plugin_timezone);
    dayjs.extend(dayjs_plugin_updateLocale);
    // dayjs.extend(dayjs_plugin_duration);

    if ('' !== _.timezone) {
      dayjs.tz.setDefault(_.timezone);

      if (/^Australia/.test(_.timezone)) {
        dayjs.updateLocale('en', {
          formats: {
            LT: "h:mm A",
            LTS: "h:mm:ss A",
            L: "DD/MM/YYYY",
            l: "D/M/YYYY",
            LL: "MMMM Do YYYY",
            ll: "MMM D YYYY",
            LLL: "MMMM Do YYYY h:mm A",
            lll: "MMM D YYYY h:mm A",
            LLLL: "dddd, MMMM Do YYYY h:mm A",
            llll: "ddd, MMM D YYYY h:mm A"
          }
        });
      }
    }
  });

  _.dayjs = (a, b, c, d) => {
    let r = false;

    if ('DD/MM/YYYY' == b) {
      let _a = String(a).split('/');
      if (3 == _a.length) {
        // console.log( _a, Number( _a[1]));

        _a[1] = Number(_a[1]) - 1; //Javascript months are 0-11
        // console.log( _a);

        a = new Date(_a[2], _a[1], _a[0]);

      }

    }

    r = dayjs(a, b, c, d);
    if (!!r.tz && '' !== _.timezone) r.tz(_.timezone);
    return (r);

  };

  _.asDayJS = d => new Promise(resolve => {
    let djs = _.dayjs(d);
    if (djs.isValid() && djs.unix() > 0) resolve(djs);
  });

  // https://blog.saviomartin.com/20-killer-javascript-one-liners
  // _.isDateValid = (...val) => !Number.isNaN(new Date(...val).valueOf());
  _.isDateValid = s => {

    if (!s) return false;

    let d = _.dayjs(s);
    if (d.isValid() && d.unix() > 0) return true;

    return false;
  };

  _.getMeta = (mName) => {
    let metas = document.getElementsByTagName('meta');

    for (let i = 0; i < metas.length; i++) {
      if (metas[i].getAttribute('name') === mName) {

        return metas[i].getAttribute('content');
      }

    }

    return '';
  }

  _.moment = (a, b, c, d) => {
    /**
    * if you call this and the moment library
    * is undefined it will error (der)
    *
    * The intention is that:
    *	a. the library will be loaded
    *	b. you could/will redefine this function to control
    * 		 the timezone being operated in
    */

    let r = moment(a, b, c, d);
    // d.utcOffset( desirable timezone);
    return (r);

  };

  _.nav = (_url, withProtocol) => {
    if (_.browser.isIPhone) {
      $(window).on('pagehide', () => _.hourglass.off());

    }
    else {
      $(window).on('unload', () => _.hourglass.off());

    };

    _.hourglass.on();
    window.location.href = _.url(_url, withProtocol);

  };

  const generateRandomInt = (min, max) => {
    return Math.floor((Math.random() * (max + 1 - min)) + min);
  };

  // https://dev.to/ovi/20-javascript-one-liners-that-will-help-you-code-like-a-pro-4ddc
  _.randomString = () => 'abcdefghijklmnopqrstuvwxyz'.charAt(generateRandomInt(0, 25)) + Math.random().toString(36).slice(2)

  _.timer = () => {
    return (new function () {
      this.start = new Date();
      this.elapsed = () => {
        let now = new Date();
        let timeDiff = now - this.start; //in ms

        timeDiff /= 10; // strip the ms
        return Math.round(timeDiff) / 100; // return seconds
      };

    });

  };

  _.pdflib = () => {

    return 'undefined' == typeof PDFLib ?
      new Promise(resolve => {

        _.get.script(_.url("assets/pdflib/"))
          .then(() => resolve());
      }) :
      Promise.resolve();
  };

  _.tiny = () => {
    return 'undefined' == typeof tinyMCE ?
      new Promise(resolve => {

        _.get.script(_.url("js/tinymce5/"))
          .then(() => resolve());
      }) :
      Promise.resolve();

  };

  // _.tiny6 = () => {
  //   return 'undefined' == typeof tinyMCE ?
  //     _.get.script(_.url("assets/tinymce/")) :
  //     Promise.resolve();

  // };

  _.urlwrite = _.url = (_url, withProtocol) => {
    if ('undefined' == typeof _url)
      _url = '';

    let prefix = !!withProtocol ? location.protocol + '//' : '/'
    return (prefix + _url);
  };
})(_brayworth_);
