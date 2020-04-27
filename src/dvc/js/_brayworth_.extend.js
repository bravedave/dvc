/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */
/*jshint esversion: 6 */
_brayworth_.extend = function() {

    // Variables
    let extended = {};
    let deep = false;
    let i = 0;
    let length = arguments.length;

    // Check if a deep merge
    if ( '[object Boolean]' === Object.prototype.toString.call(arguments[0])) {
        deep = arguments[0];
        i++;

    }

    // Loop through each object and conduct a merge
    for (; i < length; i++) {
        ( (obj) => {
            // Merge the object into the extended object
            for (var prop in obj) {
                if ( Object.prototype.hasOwnProperty.call( obj, prop)) {
                    // If deep merge and property is an object, merge properties
                    if ( deep && Object.prototype.toString.call( obj[ prop]) === '[object Object]') {
                        extended[prop] = _brayworth_.extend( true, extended[ prop], obj[ prop]);

                    }
                    else {
                        extended[prop] = obj[prop];

                    }

                }

            }

        })( arguments[i]);

    }

    return extended;

}
;