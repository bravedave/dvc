<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/  ?>

<ul class="nav flex-column">
  <li class="nav-item">
    <a class="nav-link active" aria-current="page" href="#">Active</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#">Link</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#" id="<?= $_uidAlert = strings::rand() ?>">Alert</a>
  </li>
  <li class="nav-item">
    <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
  </li>
</ul>
<script>
  (_ => {
    $('#<?= $_uidAlert ?>').on( 'click', function( e) {
      e.stopPropagation();e.preventDefault();

      _.ask.alert({
        text:'how you doin ?',
        buttons : {
          'ok' : function(e) { this.modal('hide')}
        }
      })

    });

  })(_brayworth_);
</script>
