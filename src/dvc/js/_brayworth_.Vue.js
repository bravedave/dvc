/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */
(_ => {
  _.Vue = params => {
    let options = {
      ...{
        filters: {}
      }, ...params
    };

    options.filters = {
      ...{
        capitalize: value => {
          if (!value) return '';
          return value.toCapitalCase();
        }
      }, ...filters
    };

    return new Promise(resolve => {
      if ('undefined' === typeof Vue) {
        _.get.script(_.url('js/vue.min.js')).then(() => {
          console.log('vuejs dynamically loaded ...')
          resolve(new Vue(options));
        });
      }
      else {
        resolve(new Vue(options));
      }
    });
  };
})(_brayworth_);
