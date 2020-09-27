<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/  ?>

<footer></footer>
<script>
  $(document).on('get-content', (e, j) => {
    const els = [
      'header',
      'nav',
      'main',
      'footer'

    ];

    $.each( j, (k, v) => {
      if ( els.indexOf(k) > -1) {
        fetch( v)
        .then( response => response.text())
        .then( html => $(k).html(html));

      }

    });

  });

</script>
