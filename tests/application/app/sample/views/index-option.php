<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/  ?>

<div class="row">
  <div class="col">
    <div class="form-check">
      <input type="checkbox" name="TheOption" value="yes"
        <?php if ( 'yes' == sys::option('TheOption')) print 'checked'; ?>
        id="<?= $_uid = strings::rand() ?>">
      <label class="form-check-label" for="<?= $_uid ?>">The Option</label>

    </div>

  </div>

</div>
<script>
( _ => {
  $('#<?= $_uid ?>').on( 'change', function( e) {
    let _me = $(this);

    _.post({
      url : _.url('<?= $this->route ?>'),
      data : {
        action : 'set-option',
        key : _me.attr( 'name'),
        val : _me.val()

      },

    }).then( d => _.growl( d));

  });

}) (_brayworth_);
</script>
