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
  <li class="nav-item text-center border-top mt-4 pt-2">
    <button class="btn btn-outline-primary" id="<?= $_uid = strings::rand() ?>">modal</button>

  </li>

</ul>
<script>
  ( _ => {
    $('#<?= $_uid ?>').on( 'click', function( e) {
      e.stopPropagation();

      _.get.modal(_.url('<?= $this->route ?>/samplemodal'))

    });
  }) (_brayworth_);
</script>
