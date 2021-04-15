<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/  ?>

<nav class="nav flex-column">
  <div class="nav-item"><a class ="nav-link" href="#" id="<?= $_uid = strings::rand()  ?>">Create Default</a></div>

</nav>
<script>
( _ => {
  $('#<?= $_uid ?>').on( 'click', function( e) {
    e.stopPropagation();e.preventDefault();

    _.hourglass.on();

    _.post({
      url : _.url('<?= $this->route ?>'),
      data : { action : 'create-default-set' },

    }).then( d => {
      if ( 'ack' == d.response) {
        window.location.reload();

      }
      else {
        _.growl( d);

      }

    });

  });

}) (_brayworth_);
</script>
