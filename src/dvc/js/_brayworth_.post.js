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
    growl: _.growl
  }, ...opts
}))(_brayworth_);
