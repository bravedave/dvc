/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */
/*jshint esversion: 6 */
if (!window._brayworth_)
  window._brayworth_ = () => { return (window._brayworth_); };

(_ => {
  _.version = 0.2;
  _._brayworth_ = true;
  _.currentUser = false;
  _.logon_retrieve_password = false;
  _.templates = {};

  _.hideContexts = e => {
    if ( !!e) {
      e.stopPropagation();
      e.preventDefault();

    }

    $(document).trigger('hide-contexts');

  };

  _.bootstrap_version = () => {
    if (undefined != window.bootstrap) {
      if (!!bootstrap.Alert) {
        if (/^5/.test(bootstrap.Alert.VERSION)) {
          return 5;

        }
        else if (/^4/.test(bootstrap.Alert.VERSION)) {
          return 4;

        }
        else if (/3/.test(bootstrap.Alert.VERSION)) {
          return 3;

        }

      }

    }

    return 0;

  };

  _.bootstrap_version.extended = () => {
    if (undefined != window.bootstrap) {
      if (!!bootstrap.Alert) {
        return bootstrap.Alert.VERSION;

      }

    }

    return 0;

  };

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

  $(document).ready(() => {
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

  // https://blog.saviomartin.com/20-killer-javascript-one-liners
  _.isDateValid = (...val) => !Number.isNaN(new Date(...val).valueOf());

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

  // https://dev.to/ovi/20-javascript-one-liners-that-will-help-you-code-like-a-pro-4ddc
  _.randomString = () => Math.random().toString(36).slice(2);

  _.tiny = () => {
    return 'undefined' == typeof tinyMCE ?
      _.get.script(_.url("js/tinymce5/")) :
      Promise.resolve();

  };

  _.urlwrite = _.url = (_url, withProtocol) => {
    if ('undefined' == typeof _url)
      _url = '';

    let prefix = !!withProtocol ? location.protocol + '//' : '/'

    return (prefix + _url);

  };

})(_brayworth_);
