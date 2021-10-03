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
  _.extend = (...arguments) => {
    let ext = {};
    let deep = false;
    let i = 0;
    let length = arguments.length;

    // Check if a deep merge
    if ('[object Boolean]' === Object.prototype.toString.call(arguments[0])) {
      deep = arguments[0];
      i++;

    }

    let f = obj => {
      // Merge the object into the ext object
      for (let prop in obj) {
        if (Object.prototype.hasOwnProperty.call(obj, prop)) {
          // If deep merge and property is an object, merge properties
          if (deep && Object.prototype.toString.call(obj[prop]) === '[object Object]') {
            ext[prop] = _.extend(true, ext[prop], obj[prop]);

          }
          else {
            ext[prop] = obj[prop];

          }

        }

      }

    };

    // Loop through each object and conduct a merge
    for (; i < length; i++) f(arguments[i]);

    return ext;

  };

})(_brayworth_);
