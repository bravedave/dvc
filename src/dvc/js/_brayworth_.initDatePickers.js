/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * */
/*jshint esversion: 6 */
( _ => {
  _.initDatePickers = parent => {
    if ( $.fn.datepicker ) {
      if ( !parent)
        parent = 'body';

      $('.datepicker', parent).each( function( i, el ) {
        let bootstrap = (typeof $().scrollspy == 'function');
        let df = $(el).data('dateformat');
        if ( df == undefined ) {
          if ( bootstrap)
            df = 'yyyy-mm-dd';
          else if (jQuery.ui)
            df = 'yy-mm-dd';

        }

        // test if you have bootstrap
        if ( bootstrap)
          $(el).datepicker({ format : df });

        else if (jQuery.ui)
          $(el).datepicker({ dateFormat : df });


      });

    }

  };

  $(document).ready(() => _.initDatePickers());

}) (_brayworth_);
