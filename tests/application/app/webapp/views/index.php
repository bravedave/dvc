<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * styleguide : https://codeguide.co/
*/

namespace webapp;
?>

<script>
  $(document).ready( () => {
    ( _ => {
      $(document).trigger('get-content', {
        'main' : _.url( '<?= $this->route ?>/content'),
        'footer' : _.url( '<?= $this->route ?>/footer'),
        'nav' : _.url( '<?= $this->route ?>/nav'),

      });

      $('head > title').html('Hello World!')

    }) (_brayworth_);

  });
</script>
