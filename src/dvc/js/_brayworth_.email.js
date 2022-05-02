/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * _brayworth_.rfc922({email:'david@brayworth.com.au', name:'David Bray'});
 * */
(_ => {
  _.email = {}

  _.email.rfc922 = params => {
    if ('string' == typeof params) return params;

    let o = {
      ...{
        "email":"",
        "name": ""
      }, ...params
    };

    if (o.name == '') return params.email;

    return `${o.name} <${o.email}>`;
  };
})(_brayworth_);
