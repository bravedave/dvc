/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */

/*jshint esversion: 6 */
(_ => {
  _.email = {}

  _.email.rfc922 = (params) => {
    if ( 'string' == typeof params) return params;

    let o = _.extend({
      email: '',
      name: ''
    }, params);

    if (o.name == '')
      return params.email;

    let _t = '{name} <{email}>';

    return _t
      .replace(/{name}/, o.name)
      .replace(/{email}/, o.email);

  };

}) (_brayworth_);
