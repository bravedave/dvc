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
  _.Vue = params => {
    let options = _.extend( {
      filters: {}
    }, params);

    options.filters = _.extend({
      capitalize: function (value) {
        if (!value) return '';
        return value.toCapitalCase();

      }

    }, filters);

    return new Promise( resolve => {
      if ( 'undefined' === typeof Vue) {
        let s = document.createElement('script');
        s.type = 'text/javascript';
        s.src = _.url('js/vue.min.js');
        s.addEventListener('load', e => {
          console.log('vuejs dynamically loaded ...')
          resolve( new Vue( options));

        });

        document.body.appendChild(s);

      }
      else {
        resolve( new Vue( options));

      }

    });

  };

}) (_brayworth_);

