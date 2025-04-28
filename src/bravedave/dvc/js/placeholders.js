/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 */
(($, _) => {

  $.fn.placeholders = function (count = 12) {

    if ('TABLE' == String(this[0].nodeName)) {

      _.table._placeholders_(this, count);
    } else {

      console.log(`cannot placeholder : ${this[0].nodeName}`);
    }

    return this;
  }

  $.fn.clearPlaceholders = function () {

    if ('TABLE' == String(this[0].nodeName)) {

      _.table._clear_placeholders_(this);
    } else {

      console.log(`cannot clear placeholders : ${this[0].nodeName}`);
    }

    return this;
  }
})(jQuery, _brayworth_);
