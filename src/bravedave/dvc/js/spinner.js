/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 */
($ => {

  $.fn.spinner = function (state) {

    if (this.length > 0) {

      let _data = this.data();
      if ('off' == String(state)) {
        this.removeClass().addClass(_data.class);
      } else {

        if (!_data.class) this.data('class', this.attr('class'));

        this.removeClass();
        this.addClass('grow' == String(state) ? 'spinner-grow spinner-grow-sm' : 'spinner-border spinner-border-sm');
      }
    }

    return this;
  }
})(jQuery);
