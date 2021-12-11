/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */
(_ => _.post = opts => $.ajax({
  ...{
    url: _.url(),
    type: 'POST',
    data: {},
    growl: d => _.growl(d),

  }, ...opts
}))(_brayworth_);
