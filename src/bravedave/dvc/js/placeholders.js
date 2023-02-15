/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * https://learn.jquery.com/plugins/basic-plugin-creation/
 *
 * */
($ => {
  $.fn.placeholders = function () {

    if ('TABLE' == String(this[0].nodeName)) {

      _.table.placeholders(this);
    } else {

      console.log(`cannot placeholder : ${this[0].nodeName}`);
    }
  }
})(jQuery);
