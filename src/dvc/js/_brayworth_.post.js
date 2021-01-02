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
  _.post = param => {
    let opts = _.extend({
      url : _.url(),
      type : 'POST',
      data : {},
      growl : d => $('body').growlAjax( d),

    }, param);

    return $.ajax(opts);

  };

}) (_brayworth_);

