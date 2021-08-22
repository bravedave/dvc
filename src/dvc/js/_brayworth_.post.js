/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */
(_ => _.post = opts => {
  return $.ajax(
    _.extend({
      url: _.url(),
      type: 'POST',
      data: {},
      growl: d => _.growl(d),

    }, opts)
  );

})(_brayworth_);
