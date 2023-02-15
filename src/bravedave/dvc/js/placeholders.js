/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 */
($ => {

  $.fn.placeholders = function () {

    if ('TABLE' == String(this[0].nodeName)) {

      _brayworth_.table._placeholders_(this);
    } else {

      console.log(`cannot placeholder : ${this[0].nodeName}`);
    }

    return this;
  }
})(jQuery);
