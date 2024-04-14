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
        email_validated: '',
        name: str.replace(/<[^>]*>$/g, '').trim()
      };

      if ('' == r.name) r.name = r.email;
      return r;
    }

    return false;
  };

  _.email.validated = email => {

    let e = String(email);

    if (e.length < 5) return '';

    // https://stackoverflow.com/questions/46155/how-can-an-email-address-be-validated-in-javascript
    let rex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

    if (/.con$/i.test(e.toLowerCase())) return ''; // dickhead
    if (rex.test(e.toLowerCase())) return e;

    return '';
  };

  _.email.rfc922 = _.email.rfc822;  // wft, oops blunder - have used this for a while
})(_brayworth_);
