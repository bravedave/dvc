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
    <div class="dropdown" id="<?= $_uid = strings::rand() ?>">
      <button class="btn btn-secondary dropdown-toggle"
        type="button" id="dropdownMenuButton"
        <?= dvc\bs::data('toggle', 'dropdown') ?> aria-haspopup="true" aria-expanded="false">
          Dropdown button

      </button>

      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <a class="dropdown-item" href="#">Action</a>
        <a class="dropdown-item" href="#">Another action</a>
        <a class="dropdown-item" href="#">Something else here</a>

      </div>

    </div>

  </div>

</div>
<script>
$('#<?= $_uid ?>').on( 'hidden.bs.dropdown', (e) => console.log( e));
</script>

<div class="row my-4">
  <div class="col py-4 border" id="<?= $_uid = strings::rand() ?>">
      right click me for a context menu

  </div>

</div>

<div class="row my-4">
  <div class="col py-4 border">
<pre><code class="language-javascript">
<b>Code</b>
( _ => {
  $(document).on( 'edit-person', e => _.get.modal( _.url( '<?= $this->route ?>/editPerson')));

  $('button').on( 'click', e => $(document).trigger('edit-person'));

})( _brayworth_);
</code></pre>

    <button class="btn btn-outline-secondary" id="<?= $_btn = strings::rand() ?>">edit a person</button>

  </div>

</div>
<script>
( _ => {
  $(document).on( 'edit-person', e => _.get.modal( _.url( '<?= $this->route ?>/editPerson')));

  $('#<?= $_btn ?>').on( 'click', e => $(document).trigger('edit-person'));

  $('#<?= $_uid ?>').on( 'contextmenu', function( e) {
    if ( e.shiftKey)
        return;

    e.stopPropagation();e.preventDefault();

    $(document).trigger('hide-contexts');

    let _context = _brayworth_.context();

    _context.append( $('<a href="#">hello</a>').on( 'click', function( e) {
      _context.close();

    }));

    _context.append( $('<a href="#">edit a person</a>').on( 'click', function( e) {
      _context.close();
      $(document).trigger('edit-person')

    }));

    _context.open( e);

  });

})( _brayworth_);
</script>
