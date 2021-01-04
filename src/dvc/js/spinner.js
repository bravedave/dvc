/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */

( $ => {
  $.fn.spinner = function( state) {
    if ( 'off' == String( state)) {
      let _data = this.data();

      this.removeClass().addClass( _data.class);
      this.removeData( 'class');

    }
    else {

      this.data( 'class', this.attr( 'class'));
      this.removeClass();
      this.addClass( 'grow' == String( state) ? 'spinner-grow spinner-grow-sm' :'spinner-border spinner-border-sm');

    }

    return this;

  }

}) (jQuery);
