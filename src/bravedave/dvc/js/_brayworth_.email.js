/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * _brayworth_.rfc822({email:'david@brayworth.com.au', name:'David Bray'});
 * */
(_ => {
  _.email = {}

  _.email.rfc822 = params => {
    if ('string' == typeof params) return params;

    let o = {
      ...{
        "email": "",
        "name": ""
      }, ...params
    };

    if (o.name == '') return params.email;

    return `${o.name} <${o.email}>`;
  };

  _.email.address = str => {

    if ((String(str).isEmail())) {

      let r = {
        email: str.replace(/(^[^<]*<|>$)/g, ''),
        name: str.replace(/<[^>]*>$/g, '').trim()
      };

      if ('' == r.name) r.name = r.email;
      return r;
    }

    return false;
  };

  _.email.rfc922 = _.email.rfc822;  // wft, oops blunder - have used this for a while
})(_brayworth_);
