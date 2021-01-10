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
  _.InitHRefs = () => {
    $('[data-href]').each( function( i, el ) {
      $(el).css({'cursor':'pointer'}).off('click').on('click', function( e) {
        if ( /^(a)$/i.test( e.target.nodeName ))
          return;

        e.stopPropagation(); e.preventDefault();

        if ( $(e.target).closest( '[data-role="contextmenu"]' ).length > 0 )
          _.hideContext( $(e.target).closest( '[data-role="contextmenu"]' )[0]);

        let target = $(this).data('target');
        if ( target == '' || target == undefined )
          window.location.href = $(this).data('href');

        else
          window.open( $(this).data('href'), target);

      });

    });

  };

  $(document).ready(() => _.InitHRefs());

}) (_brayworth_);
